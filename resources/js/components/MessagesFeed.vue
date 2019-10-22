<template>
    <div class="feed" ref="feed">
        <ul v-if="contact">
            <li v-for="message in messages" :key="message.id" :class="`message ${message.to===contact.id?'sent':'received'}`">
                <div class="text">
                    {{message.message}}
                </div>
                <p v-if="message.to===contact.id && message.read == 1">Leído</p>
            </li>
        </ul>
    </div>
</template>
<script>
import { setTimeout } from 'timers';
export default {
    props:{
        contact:{
            type:Object
        },
        messages:{
            type:Array,
            required:true
        }
    },
    methods:{
        scrollToBottom(){
            setTimeout(()=>{
                this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight;
            },50);
        }
    },
    watch:{
        contact(contact){
            this.scrollToBottom();
        },
        messages(messages){
            this.scrollToBottom();
        }
    }
}
</script>
<style lang="scss" scoped>
.feed{
    background:#f0f0f0;
    height:100%;
    max-height:435px;
    overflow: scroll;

    ul{
        list-style-type:none;
        padding:0px;

        li {
            &.message{
                margin:10px 0;
                width:100%;

                .text{
                    max-width:200px;
                    border-radius:5px;
                    padding:12px;
                    display:inline-block;
                }
                p{
                    color:#7e8694;
                    font-size: 12px;
                    margin-right: 10px;
                }
                &.received{
                    text-align: left;
                    
                    .text{
                        background:rgba(221, 228, 240,1);
                        margin-left:10px;
                    }
                }

                &.sent{
                    text-align: right;
                    
                    .text{
                        background:rgba(0, 132, 255, 1);
                        margin-right:10px;
                        color: #ffffff;
                    }
                }
            }
        }
    }
    
}
</style>


