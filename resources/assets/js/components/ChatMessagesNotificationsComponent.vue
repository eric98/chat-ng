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
                                <img src="/img/photo1.png" class="img-circle" alt="User Image"/>
                            </div>
                            <h4>
                                {{ message.user }}
                                <small><i class="fa fa-clock-o"></i>{{ moment(message.created_at).format('LL') }}</small>
                                <!--<small><i class="fa fa-clock-o"></i>{{ moment().duration(message.created_at).humanize() }}</small>-->
                            </h4>
                            <p>{{ message.text }}</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="footer" @click="mostrarTotes()"><a href="#">Veure totes</a></li>
            <li class="footer" @click="llegirTotes()"><a href="#">Llegir totes</a></li>
        </ul>
    </li>
    <li v-else="this.seeUnreadMessages" class="dropdown messages-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-envelope-o"></i>
            <span class="label label-success">{{this.totalMessages.length}}</span>
        </a>
        <ul class="dropdown-menu">
            <li class="header">Tens {{this.totalMessages.length}} missatges de xat pendents</li>
            <li>
                <ul class="menu">
                    <li v-for="message in totalMessages" @click="llegirNotificacio(message)">
                        <a href="#">
                            <div class="pull-left">
                                <img src="/img/photo1.png" class="img-circle" alt="User Image"/>
                            </div>
                            <h4>
                                {{ message.user }}
                                <small><i class="fa fa-clock-o"></i>{{ moment(message.created_at).format('LL') }}</small>
                                <!--<small><i class="fa fa-clock-o"></i>{{ moment().duration(message.created_at).humanize() }}</small>-->
                            </h4>
                            <p>{{ message.text }}</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li v-if="!this.seeUnreadMessages" class="footer" @click="mostrarNoLlegides()"><a href="#">Veure No Llegides</a></li>
            <li class="footer" @click="llegirTotes()"><a href="#">Llegir totes</a></li>
        </ul>
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
      }
    },
    props: {
      notifications: {
        type: Array,
        required: true
      }
    },
    methods: {
      moment: function () {
        return moment();
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
      }
    },
    mounted() {
      this.getMessagesOfNotifications(this.notifications)
      Echo.join('newChatMessage.1')
        .listen('newChatMessage', e => {
//          console.log('Nova notificacio')
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
          console.log(this.unreadMessages)
          this.unreadMessages.push()
          console.log('notificacions actualitzades!')
        })
    }
  }
</script>