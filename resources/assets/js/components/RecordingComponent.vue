<template>
    <div>
        <button class="button red-button" v-on:click.stop.prevent="toggleRecording">
            <i class="stop icon" v-show="isRecording"></i>
            <i class="record icon" v-show="!isRecording"></i>
            <span v-show="!isRecording">Start recording</span>
            <span v-show="isRecording">Stop recording</span>
        </button>
        <button class="button green-button" v-if="dataUrl.length > 0" v-on:click.stop.prevent="togglePlay">
            <i class="play icon"></i> Play recording
        </button>
        <button class="remove-recording" v-if="dataUrl.length > 0" v-on:click.stop.prevent="removeRecording">
            <i class="remove icon"></i> Delete recording
        </button>
        <button class="button green-button" v-if="dataUrl.length > 0" v-on:click.stop.prevent="submitRecording">
            <i class="send icon"></i> Send recording
        </button>
        <button class="button green-button" v-on:click.stop.prevent="getLastRecording">
            <i class="send icon"></i> Get last recording
        </button>
        <audio id="audio" preload="auto" controls :src="dataUrl"></audio>
    </div>
</template>

<style>

</style>

<script>
  export default {
    data() {
      return {
        isRecording: false,
        audioRecorder: null,
        recordingData: [],
        dataUrl: '',
        audio: []
      }
    },
    methods:
      {
        getLastRecording: function () {
          axios.get('/recording/1').then((audio) => {
            console.log(audio.data)
            console.log(audio.data.path+'/'+audio.data.filename)
            var nouAudio = new Blob('/recording/1', {type : 'application/json'})
//            nouAudio.play()
//            this.dataUrl = audio.data.path+'/'+audio.data.filename
            console.log(nouAudio)
//            console.log(this.audio)
//            console.log(this.audio[this.audio.length-2])
//            this.audio[this.audio.length-2].play()
          })
//          console.log(axios.get('/recording/1'))
//          console.log(aux['filename'])
//          new Blob(axios.get('/recording/1'), { type: 'audio/ogg'})
//          this.audio[this.audio.length-1].play()
        },
        toggleRecording: function() {
          var that = this;
          this.isRecording = !this.isRecording;
          if (this.isRecording) {
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
            navigator.getUserMedia({
              audio: true,
              video: false
            }, function(stream) {
              that.stream = stream;
              that.audioRecorder = new MediaRecorder(stream, {
                mimeType: 'audio/ogg',
                audioBitsPerSecond : 96000
              });
              that.audioRecorder.start();
              console.log('Media recorder started');
            }, function(error) {
              alert(JSON.stringify(error));
            });

          }
          else {
            this.audioRecorder.stop();
            this.audioRecorder.ondataavailable = function(event) {
              that.recordingData = []
              that.recordingData.push(event.data);
            }
            this.audioRecorder.onstop = function(event) {
              console.log('Media recorder stopped');
              var blob = new Blob(that.recordingData, { type: 'audio/ogg'});
              that.dataUrl = window.URL.createObjectURL(blob);

              var formData = new FormData();
              formData.append('recording', blob);
              axios.post('/recording', formData)
            }
          }
        },
        removeRecording: function() {
          this.isRecording = false;
          this.recordingData = [];
          this.dataUrl = '';
        },
        togglePlay: function() {
          console.log('play')
          this.audio.push(new Audio(this.dataUrl))
          this.audio[this.audio.length-1].play()
          console.log(this.audio[this.audio.length-1])
//          axios.post('/recording', this.audio[this.audio.length-1])
        },
        submitRecording: function() {
          var that = this;
          var blob = new Blob(that.recordingData , { type: 'audio/ogg'});
          var formData = new FormData();
          console.log('formData: ', formData)
          console.log('blob: ', blob)
          formData.append('recording', blob);
          console.log('formData: ', formData)
          axios.post('/recording', formData)
        }
      },
    mounted() {

    }
  }
</script>