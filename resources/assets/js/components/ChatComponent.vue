<template>
    <div>
        <div class="box box-warning direct-chat direct-chat-warning">
            <div class="box-header with-border">
                <h3 class="box-title">{{ chat.name }}</h3>

                <div class="box-tools pull-right">
                    <span data-toggle="tooltip" title="" class="badge bg-yellow" data-original-title="3 New Messages">3</span>
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="" data-widget="chat-pane-toggle" data-original-title="Contacts">
                        <i class="fa fa-comments"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="direct-chat-messages" ref="chat">
                    <div class="direct-chat-msg" :class="{ right: own(message) }"v-for="message in internalMessages">
                        <div class="direct-chat-info clearfix">
                            <span class="direct-chat-name pull-left">{{ message.user.name }}</span>
                            <span class="direct-chat-timestamp pull-right"> {{ message.formatted_created_at_date }}</span>
                        </div>
                        <img class="direct-chat-img" :src="message.user.avatar" alt="message user image">
                        <div class="direct-chat-text">
                            <button v-if="isFirefox" type="button" class="btn btn-primary btn-flat" @click.prevent="playMessageAsAudio(message.body,true)"><i class="fa fa-play"></i></button>
                            {{ message.body }}
                        </div>
                    </div>
                </div>

                <div class="direct-chat-contacts">
                    <ul class="contacts-list">
                        <li v-for="participant in participants">
                            <a href="#">
                                <img class="contacts-list-img" :src="participant.avatar" alt="User Image">

                                <div class="contacts-list-info">
                                <span class="contacts-list-name">
                                  {{ participant.name }}
                                  <small class="contacts-list-date pull-right">{{ participant.created_at }}</small>
                                </span>
                                    <span class="contacts-list-msg">{{ participant.email }}</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="box-footer">
                <div class="input-group">
                    <input type="text" name="message" placeholder="Type Message ..." class="form-control" v-model="message" @keyup.enter.prevent="send">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-warning btn-flat" @click.prevent="send" >Send</button>
                        <!--<button type="button" class="btn btn-info btn-flat" @click.prevent="sendFile"><i class="fa fa-paperclip"></i></button>-->
                    </span>
                </div>
            </div>
        </div>
        <button
                @click="togglePush"
                :disabled="pushButtonDisabled || loading"
                type="button" class="btn btn-primary"
                :class="{ 'btn-primary': !isPushEnabled, 'btn-danger': isPushEnabled }">
            {{ isPushEnabled ? 'Disable' : 'Enable' }} Push Notifications
        </button>
        <button @click="viewHtmlChat" class="btn btn-success fa  fa-print"></button>
        <button @click="downloadPdf" class="btn btn-warning fa  fa-file-pdf-o"></button>
        <button v-if="isFirefox" @click="escoltarXat(0)" class="btn btn-info fa fa-bullhorn"></button>
        <audio id="audio" preload="auto" :src="srcAudioUrl" hidden></audio>
    </div>
</template>

<script>
  import moment from 'moment'

  export default {
    data() {
      return {
        isChrome: null,
        isFirefox: null,
        loading: false,
        isPushEnabled: false,
        pushButtonDisabled: true,
        internalMessages: this.chat.messages,
        participants: [],
        message:'',
        logged_user: JSON.parse(window.user),
        srcAudioUrl:''
      }
    },
    props: {
      chat: {
        type: Object,
        required: true
      }
    },
    methods: {
      /**
       * Toggle push notifications subscription.
       */
      togglePush () {
        if (this.isPushEnabled) {
          this.unsubscribe()
        } else {
          this.subscribe()
        }
      },

      /**
       * Subscribe for push notifications.
       */
      subscribe () {
        navigator.serviceWorker.ready.then(registration => {
          const options = { userVisibleOnly: true }
          const vapidPublicKey = window.Laravel.vapidPublicKey

          if (vapidPublicKey) {
            options.applicationServerKey = this.urlBase64ToUint8Array(vapidPublicKey)
          }

          registration.pushManager.subscribe(options)
            .then(subscription => {
              this.isPushEnabled = true
              this.pushButtonDisabled = false

              this.updateSubscription(subscription)
            })
            .catch(e => {
              if (Notification.permission === 'denied') {
                console.log('Permission for Notifications was denied')
                this.pushButtonDisabled = true
              } else {
                console.log('Unable to subscribe to push.', e)
                this.pushButtonDisabled = false
              }
            })
        })
      },

      /**
       * https://github.com/Minishlink/physbook/blob/02a0d5d7ca0d5d2cc6d308a3a9b81244c63b3f14/app/Resources/public/js/app.js#L177
       *
       * @param  {String} base64String
       * @return {Uint8Array}
       */
      urlBase64ToUint8Array (base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
          .replace(/\-/g, '+')
          .replace(/_/g, '/')

        const rawData = window.atob(base64)
        const outputArray = new Uint8Array(rawData.length)

        for (let i = 0; i < rawData.length; ++i) {
          outputArray[i] = rawData.charCodeAt(i)
        }

        return outputArray
      },

      /**
       * Unsubscribe from push notifications.
       */
      unsubscribe () {
        navigator.serviceWorker.ready.then(registration => {
          registration.pushManager.getSubscription().then(subscription => {
            if (!subscription) {
              this.isPushEnabled = false
              this.pushButtonDisabled = false
              return
            }

            subscription.unsubscribe().then(() => {
              this.deleteSubscription(subscription)

              this.isPushEnabled = false
              this.pushButtonDisabled = false
            }).catch(e => {
              console.log('Unsubscription error: ', e)
              this.pushButtonDisabled = false
            })
          }).catch(e => {
            console.log('Error thrown while unsubscribing.', e)
          })
        })
      },

      /**
       * Send a request to the server to update user's subscription.
       *
       * @param {PushSubscription} subscription
       */
      updateSubscription (subscription) {
        const key = subscription.getKey('p256dh')
        const token = subscription.getKey('auth')

        const data = {
          endpoint: subscription.endpoint,
          key: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
          token: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null
        }

        this.loading = true

        axios.post('/subscriptions', data)
          .then(() => { this.loading = false })
      },

      /**
       * Send a requst to the server to delete user's subscription.
       *
       * @param {PushSubscription} subscription
       */
      deleteSubscription (subscription) {
        this.loading = true

        axios.post('/subscriptions/delete', { endpoint: subscription.endpoint })
          .then(() => { this.loading = false })
      },

      own(message) {
        return message.user.id === this.logged_user.id
      },
      escoltarXat(index) {
        var text = ''
        var numMessatges = this.internalMessages.length
        text = text+' '+ this.internalMessages[index].user.name + ' diu '+ this.internalMessages[index].body
        this.playMessageAsAudio(text,false)
        setTimeout(() => {
          var audio = document.getElementById('audio')
          audio.play()
            setTimeout(() => {
              audio.pause()
              audio.currentTime = 0
              if (index+1 != numMessatges){
                this.escoltarXat(index+1)
              }
            },audio.duration*1000)
        },500)
      },
      playMessageAsAudio(text,play) {
        var url
        if (this.isFirefox){
          text = encodeURIComponent(text)
          url = "https://translate.google.com/translate_tts?ie=UTF-8&q=" + text + "&tl=es&client=tw-ob"
          this.srcAudioUrl = url
          setTimeout(() => {
            var audio = document.getElementById('audio')
            if (play){
              audio.play()
              setTimeout(() => {
                audio.pause()
                audio.currentTime = 0
              },audio.duration*1000)
            }
          },500)
        }
      },
//      sendFile(){
//
//      },
      send() {
        axios.post('/chat/' + this.chat.id + '/message', {
          'body': this.message,
          'user' : this.logged_user,
          'participants': this.participants
        }).then(response => {
          const message = {
            'body':  this.message,
            'chat_id': this.chat.id,
            'formatted_created_at_date': this.timestamp(),
            user: {
              'name': this.logged_user.name,
              'avatar': this.logged_user.avatar,
              'id': this.logged_user.id
            }
          }
          this.internalMessages.push(message)
        }).catch(error => {
          console.log('Error')
          console.log(error)
        }).then(() => {
          this.scroll_top_down()
          this.message = ''
        })
      },
      scroll_top_down() {
        this.$refs.chat.scrollTop = this.$refs.chat.scrollHeight;
      },
      timestamp() {
        return moment().format('YYYY-MM-DD hh:mm:ss')
      },
      /**
       * Register the service worker.
       */
      registerServiceWorker () {
        if (!('serviceWorker' in navigator)) {
          console.log('Service workers aren\'t supported in this browser.')
          return
        }

        navigator.serviceWorker.register('/sw.js')
          .then(() => this.initialiseServiceWorker())
      },

      initialiseServiceWorker () {
        if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
          console.log('Notifications aren\'t supported.')
          return
        }

        if (Notification.permission === 'denied') {
          console.log('The user has blocked notifications.')
          return
        }

        if (!('PushManager' in window)) {
          console.log('Push messaging isn\'t supported.')
          return
        }

        navigator.serviceWorker.ready.then(registration => {
          registration.pushManager.getSubscription()
            .then(subscription => {
              this.pushButtonDisabled = false

              if (!subscription) {
                return
              }

              this.updateSubscription(subscription)

              this.isPushEnabled = true
            })
            .catch(e => {
              console.log('Error during getSubscription()', e)
            })
        })
      },
      downloadPdf() {
        window.location.href = this.chat.id + '/pdf'
      },
      viewHtmlChat() {
        window.location.href = this.chat.id + '/view'
      }
    },
    mounted () {
      this.isFirefox = typeof InstallTrigger !== 'undefined'
      this.isChrome = !!window.chrome && !!window.chrome.webstore
      this.scroll_top_down()
      this.registerServiceWorker()
      Echo.join('newChatMessage.'+this.chat.id)
        .listen('newChatMessage', e => {
          const message = {
            'body':  e.message,
            'chat_id': e.chat.id,
            'formatted_created_at_date': this.timestamp(),
            user: {
              'name': e.user.name,
              'avatar': e.user.avatar,
              'id': e.user.id
            }
          }
          this.chat.messages.push(message)
          Vue.nextTick(() => {
            this.scroll_top_down()
          })
        })
        .here(users => {
          this.participants = users
        })
        .joining(user => {
          this.participants.push(user)
        })
        .leaving(user => {
          this.participants.splice(this.participants.indexOf(user),1)
        })
    },
  }
</script>
