<template>
    <li class="dropdown messages-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-envelope-o"></i>
            <span class="label label-success">4</span>
        </a>
        <ul class="dropdown-menu">
            <li class="header">Tens 4 missatges de xat pendents</li>
            <li>
                <ul class="menu">
                    <li v-for="message in internalMessages">
                        <a href="#">
                            <div class="pull-left">
                                <img src="/img/photo1.png" class="img-circle" alt="User Image"/>
                            </div>
                            <h4>
                                {{ message.user }}
                                <small><i class="fa fa-clock-o"></i>{{ message.created_at }}</small>
                            </h4>
                            <p>{{ message.text }}</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="footer"><a href="#">Veure totes</a></li>
        </ul>
    </li>
</template>

<style>

</style>

<script>
  export default {
    data() {
      return {
        internalMessages: this.messages
//        internalMessages: [
//          {
//            user:'Pepito',
//            text:'Que paixa tio!',
//            created_at:'1 minute ago',
//          },
//          {
//            user:'Maria',
//            text:'Que paixa tio!',
//            created_at:'1 minute ago',
//          },
//          {
//            user:'Quim',
//            text:'Que paixa tio!',
//            created_at:'1 minute ago',
//          }
//        ]
      }
    },
    props: {
      messages: {
        type: Array,
        required: true
      }
    },
    mounted() {
      console.log('Mounted ok')
    }
  }
</script>