<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="title-header">
                    <div class="container">
                        <div class="row">
                            <div class="col"></div>
                            <div class="col">
                                <h2 class="headline text-center my-3">
                                     <b>To Do</b> <b style="color:red;"> List</b>
                                </h2>
                            </div>
                            <div class="col align-self-center d-flex justify-content-end">
                                <button
                                    class="btn btn-danger font-weight-bold"
                                    :disabled="appendTaskFormShow"
                                    @click="appendTaskFormShow=true">
                                    + Task
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="!appendTaskFormShow && tasks.length === 0 && doneTasks.length === 0">
                    <h3 class="text-center mt-5">Start creating some tasks with <b style="color: red">+ Task</b> button</h3>
                </div>
                <div v-if="appendTaskFormShow" class="mb-5">
                    <p>Add new task!</p>
                    <todo-item
                        class="mt-2"
                        :task="appendTaskFormData"
                        v-if="appendTaskFormShow"
                        @newTask="addTask"
                        @cancelAppend="appendTaskFormShow=false"
                        :is-append-form="true"/>
                </div>
                <div v-if="tasks.length > 0" class="mb-5">
                    <p>My pending tasks</p>
                    <todo-item
                        class="mt-2"
                        :task="task"
                        :key="task.id"
                        @completed="endTask($event)"
                        @updateTask="updateTask($event)"
                        @deleteTask="deleteTask($event)"
                        v-for="task in tasks"/>
                </div>
                <div v-if="doneTasks.length > 0">
                    <p>Completed tasks</p>
                    <todo-item
                        class="mt-2"
                        :task="task"
                        :key="task.id"
                        :isDone="task.done"
                        @deleteTask="deleteTask($event)"
                        v-for="task in doneTasks"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import TodoListItem from "./TodoListItem";
    export default {
        components: {
            'todo-item': TodoListItem
        },
        data:() => ({
            tasks:[],
            doneTasks:[],
            appendTaskFormShow:false,
            appendTaskFormData:{
                description:'',
                priority:2,
                done:false
            },
            current_user:null
        }),
        mounted() {
            axios.get('current_user').then(
                res => {
                    this.current_user = res.data;
                    axios.get(`api/tasks?user_id=${this.current_user}`).then(res => {
                        console.log(res)
                        this.tasks = res.data.filter(x => (!x.done));
                        this.doneTasks = res.data.filter(x => (x.done));
                        this.initAppendFormData();
                    });
                });


        },
        watch:{
            appendTaskFormShow(newVal){
                if (newVal===false){
                    this.initAppendFormData()
                }
            }
        },
        methods:{
            orderTasks(){
                this.tasks.sort((a,b) => (a.priority - b.priority) || (a.id - b.id))
            },
            addTask(){
                this.appendTaskFormData.user_id = this.current_user;
                axios.post('api/tasks',this.appendTaskFormData)
                    .then(
                        res => {
                            this.tasks.push(res.data);
                            this.orderTasks();
                            this.appendTaskFormShow=false;
                        } )
            },
            deleteTask(task){
                axios.delete(`api/tasks/${task.id}`).then(
                    res => {
                        let index = this.tasks.findIndex(x => x.id === task.id);
                        if (index > -1) {
                            this.tasks.splice(index,1)
                        }else{
                            index = this.doneTasks.findIndex(x => x.id === task.id);
                            if (index > -1){
                                this.doneTasks.splice(index,1);
                            }
                        }
                    }
                );
            },
            endTask(task){
                console.log('done task', task.id);
                task.done=1;
                axios.put(`api/tasks/${task.id}`,task).then(
                    res => {
                        console.log(res);
                        const index = this.tasks.findIndex(x => x.id === res.data.id);
                        if (index > -1) {
                            this.tasks.splice(index,1);
                            this.doneTasks.push(res.data);
                        }
                    }
                )
            },
            updateTask(task){
                axios.put(`api/tasks/${task.id}`,task).then(
                    res => {
                        console.log(res);
                        const index = this.tasks.findIndex(x => x.id === res.data.id);
                        if (index > -1) {
                            this.$set(this.tasks,index,res.data);
                        }
                        this.orderTasks();
                    }
                )
            },
            initAppendFormData(){
                this.appendTaskFormData = {
                    description:'',
                    priority:2,
                    done:false,
                }
            }
        }
    }
</script>
