<template>
    <li v-if="this.seeUnreadMessages" class="dropdown messages-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-envelope-o"></i>
            <span class="label label-success">{{this.unreadMessages.length}}</span>
        </a>
        <ul class="dropdown-menu">
            <li class="header">Tens {{this.unreadMessages.length}} missatges de xat pendents</li>
            <li>
                <ul class="menu">
                    <li v-for="message in unreadMessages" @click="llegirNotificacio(message)">
                        <a href="#">
                            <div class="pull-left">
                                <img v-bind:src="message.user['avatar']" class="img-circle" alt="User Image"/>
                            </div>
                            <h4>
                                {{ message.user.name }}
                                <small><i class="fa fa-clock-o"></i>{{ moment(message.created_at).format('LL') }}</small>
                            </h4>
                            <p>{{ message.text }}</p>
                            <p>{{ message.chat.name }}</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="footer" @click="escoltarTotes(0,false)"><a href="#">Escoltar</a></li>
            <li class="footer" @click="mostrarTotes()"><a href="#">Veure totes</a></li>
            <li class="footer" @click="llegirTotes()"><a href="#">Llegir totes</a></li>
        </ul>
        <audio class="audio" preload="auto" :src="srcAudioUrl" hidden></audio>
    </li>
    <li v-else="this.seeUnreadMessages" class="dropdown messages-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-envelope-o"></i>
            <span class="label label-success">{{this.totalMessages.length}}</span>
        </a>
        <ul class="dropdown-menu">
            <li class="header">Tens {{this.totalMessages.length}} missatges de xat totals</li>
            <li>
                <ul class="menu">
                    <li v-for="message in totalMessages" @click="llegirNotificacio(message)">
                        <a href="#">
                            <div class="pull-left">
                                <img v-bind:src="message.user['avatar']" class="img-circle" alt="User Image"/>
                            </div>
                            <h4>
                                {{ message.user['name'] }}
                                <small><i class="fa fa-clock-o"></i>{{ moment(message.created_at).format('LL') }}</small>
                            </h4>
                            <p>{{ message.text }}</p>
                            <p>{{ message.chat.name }}</p>

                        </a>
                    </li>
                </ul>
            </li>
            <li class="footer" @click="escoltarTotes(0,true)"><a href="#">Escoltar</a></li>
            <li v-if="!this.seeUnreadMessages" class="footer" @click="mostrarNoLlegides()"><a href="#">Veure No Llegides</a></li>
        </ul>
        <audio class="audio" preload="auto" :src="srcAudioUrl" hidden></audio>
    </li>
</template>

<style>

</style>

<script>
  var moment = require('moment');
  import axios from 'axios'
  export default {
    data() {
      return {
        unreadMessages: [],
        totalMessages: [],
        seeUnreadMessages: true,
        srcAudioUrl:''
      }
    },
    props: {
      notifications: {
        type: Array,
        required: true
      },
      user: {
        required: true
      }
    },
    methods: {
      moment: function () {
        return moment();
      },
      escoltarTotes(index,totsMessages) {
        var text = ''
        if (totsMessages) {
          var numMessatges = this.totalMessages.length
          text = text + this.totalMessages[index].chat.name + this.totalMessages[index].user['name'] + ' diu ' + this.totalMessages[index].text
        } else {
          var numMessatges = this.unreadMessages.length
          text = text + this.unreadMessages[index].chat.name + this.unreadMessages[index].user['name'] + ' diu ' + this.unreadMessages[index].text
        }
        this.playMessageAsAudio(text,false)
        setTimeout(() => {
          var audio = document.getElementsByClassName('audio')[0]
          audio.play()
          setTimeout(() => {
            audio.pause()
            audio.currentTime = 0
            if (!totsMessages) {
              axios.post('/notifications/' + this.totalMessages[index].id + '/read')
                .then(
                  this.unreadMessages.splice(index, 1)
                )
              if (index + 1 != numMessatges) {
                this.escoltarTotes(index, totsMessages)
              }
            } else {
              if (index + 1 != numMessatges) {
                this.escoltarTotes(index+1, totsMessages)
              }
            }
          },audio.duration*1000)
        },500)
      },
      playMessageAsAudio(text,play) {
        var url
        text = encodeURIComponent(text)
        url = "https://translate.google.com/translate_tts?ie=UTF-8&q=" + text + "&tl=es&client=tw-ob"
        this.srcAudioUrl = url
        setTimeout(() => {
          var audio = document.getElementsByClassName('audio')[0]
          if (play){
            audio.play()
            setTimeout(() => {
              audio.pause()
              audio.currentTime = 0
            },audio.duration*1000)
          }
        },500)
      },
      mostrarNoLlegides(){
        this.seeUnreadMessages = true;
      },
      mostrarTotes(){
        this.seeUnreadMessages = false;
      },
      llegirTotes() {
        this.notifications.forEach((notification) => {
          axios.post('/notifications/'+notification.id+'/read')
        })
        this.unreadMessages = []
      },
      llegirNotificacio(missatge) {
        console.log(this.notifications)
        var notificacioALlegir = this.notifications.find((notification) => {
          if (missatge==notification.data){
            return notification
          }
        })
        var index = this.unreadMessages.indexOf(missatge)

        axios.post('/notifications/'+notificacioALlegir.id+'/read')
          .then(
            this.unreadMessages.splice(index, 1)
        )
      },
      getMessagesOfNotifications(notifications) {
        this.unreadMessages = []
        notifications.forEach((notification) => {
          if (notification.read_at==null) {
            this.unreadMessages.push(notification.data)
          }
          this.totalMessages.push(notification.data)
        })
      },
      timestamp() {
        return moment().format('YYYY-MM-DD hh:mm:ss')
      },
    },
    mounted() {
      this.getMessagesOfNotifications(this.notifications)
      Echo.private(`App.User.${this.user.id}`)
        .notification((notification) => {
          console.log(notification)
          const message = {
            chat: notification.chat,
            text: notification.text,
            user: notification.user,
            created_at: {
              date: this.timestamp(),
              timezone: "UTC",
              timezone_type: 3
            }
          }
          console.log('nova notificacio')
          this.notifications.push(notification)
          this.unreadMessages.push(message)
          this.totalMessages.push(message)
        });
    }
  }
</script>