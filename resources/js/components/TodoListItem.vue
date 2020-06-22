<template>
    <div class="container" :class="{'appendForm':isAppendForm}">
        <div class="card">
            <div class="row card-body align-items-center">
                <div class="col-2">
                    <div v-if="!editMode && !isAppendForm">
                        <small>Priority:</small><br>
                        <b>{{selectedPriority}}</b>
                    </div>
                    <div v-else class="mt-n3">
                        <small>Priority:</small><br>
                        <select class="custom-select" v-model="auxData.priority">
                            <option :value="priority.id" v-for="priority in priorities">{{priority.name}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-7">
                    <p class="form-text" v-if="!editMode && !isAppendForm">{{task.description}}</p>
                    <textarea
                        class="form-control "
                        :class="{'is-invalid':invalidDescription}"
                        v-else
                        required
                        rows="2"
                        v-model="auxData.description"/>
                    <div class="invalid-feedback" v-if="invalidDescription">
                        {{invalidFeedback}}
                    </div>
                </div>
                <div class="col-3 justify-content-end" v-if="isAppendForm">
                    <button class="btn btn-success"
                            @click="addTask">
                        Save
                    </button>
                    <button class="btn btn-danger"
                            @click="$emit('cancelAppend')">
                        Cancel
                    </button>
                </div>
                <div class="col-3" v-else>
                    <template v-if="editMode">
                        <button class="btn btn-success"
                                @click="updateTask">
                            Save
                        </button>
                        <button class="btn btn-danger"
                                @click="editMode=false">
                            Cancel
                        </button>
                    </template>
                    <template v-else>
                        <button class="btn btn-success"
                                v-if="task.done == 0"
                                @click="$emit('completed',auxData)">
                            Done
                        </button>
                        <button class="btn btn-primary"
                                v-if="task.done == 0"
                                @click="setEditMode()">
                            Edit
                        </button>
                        <button class="btn btn-danger"
                                @click="$emit('deleteTask',auxData)">
                            Remove
                        </button>
                    </template>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'ListItem',
        props: ['task','isAppendForm'],
        data: () => ({
            priorities:[
                { id:1, name: 'High'},
                { id:2, name: 'Normal'},
                { id:3, name: 'Low'}
            ],
            editMode:false,
            auxData:null,
            invalidDescription:false,
            invalidFeedback:'',
        }),
        computed:{
            selectedPriority(){
                return this.priorities.find(x => x.id === this.task.priority).name
            }
        },
        methods:{
            addTask(){
                if ( !this.validateDescriptionLength() ){
                    this.invalidDescription = true;
                    this.invalidFeedback = 'Description length cannot be zero';
                    return
                }
                this.$emit('newTask');
                this.editMode = false;
            },
            setEditMode(){
                this.auxData = {...this.task};
                this.editMode = true;
            },
            updateTask(){
                if ( !this.validateDescriptionLength() ){
                    this.invalidDescription = true;
                    this.invalidFeedback = 'Description length cannot be zero';
                    return
                }
                this.$emit('updateTask',this.auxData);
                this.editMode = false;
            },
            validateDescriptionLength(){
                return ( this.auxData.description.trim().length >0 )
            }
        },
        created() {
            this.auxData = this.task;
        },
        watch:{
            auxData:{
                handler(newVal,oldVal){
                    if (newVal.description.trim().length > 0){
                        this.invalidDescription = false;
                    }
                },
                deep:true
            }
        }

    }
</script>
<style scoped lang="scss">
    .appendForm{
        .card{
            background-color: #f4f6e7;
            select,option,textarea,button{
                font-size: 20px;
            }
        }
        border-radius: 3px;
        &.parent{
            margin: auto 30px;
        }
    }
</style>
