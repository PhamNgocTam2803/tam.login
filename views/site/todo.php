<div class="w-full h-screen">
    <div class="flex flex-col justify-between w-[600px] h-[600px] ml-auto mr-auto border-rose-300 mt-5" 
    x-data="{
        tasks: [],
        newTask: '',
        filter: 'all',
        addTask() {
            if (this.newTask.trim() !== '') {
                fetch('/site/add-task',{
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': yii.getCsrfToken()
                    },
                    body: JSON.stringify({text : this.newTask, done: 'not complete', delete: false})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success){
                        this.tasks.push({text: this.newTask, done: false, delete: false, id: data.id}); 
                        this.newTask = '';
                    }else{
                        alert(data.error);
                    }
                })
                .catch(error => {
                    console.log(error);
                });
            }
        },
        allTaskDone() {
            return this.tasks.every(task => task.done);
        },
        filterTask() {
            if (this.filter === 'complete'){
                return this.tasks.filter(tasks => tasks.done);
            }
            if (this.filter ==='not complete'){
                return this.tasks.filter(tasks => !tasks.done);
            }
            return this.tasks;
        },
        updateTaskStatus(task){
            fetch('/site/update-task-status',{
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': yii.getCsrfToken()
                },
                body: JSON.stringify({task_id: task.id, done: task.done})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success){
                    task.done = !task.done;
                }else{
                    alert(data.error);
                }
            })
            .catch(error => {
                console.log(error);
            });
        },
        removeTask(task){
            fetch('/site/soft-delete-task', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': yii.getCsrfToken()
                },
                body: JSON.stringify({task_id: task.id, delete: task.delete})
            })
            .then(response => response.json())
            .then(data => {
                if(data.success){
                    this.tasks = this.tasks.filter(x => x.id !== task.id);
                }else{
                    alert(data.error);
                }
            })
            .catch(error =>{
                console.log(error);
            });
        }
    }"
    x-init="tasks = await (await fetch('/site/get-data-task')).json()">
        <div>
            <div class="flex justify-between">
                <!-- Tổng kết: tasks đã làm, chưa làm -->
                <div class="flex pt-5">
                    <p class="font-bold mr-2">To dos: <span x-text="tasks.filter(task => !task.done).length"></span></p>
                    <p class="mr-2">|</p>
                    <p class="font-bold">Completed:  <span x-text="tasks.filter(task => task.done).length"></span></p>
                </div>
                <!-- Tạo select box -->
                <div> 
                    <select class="font-bold mt-5" x-model="filter">
                        <option value="all">All</option>
                        <option value="complete">Complete</option>
                        <option value="not complete">Not Complete</option>
                    </select>
                </div>
            </div>
            <!-- Hiện thông báo khi đã hoàn thành toàn bộ task! -->
            <div x-show="allTaskDone()">
                <p class="font-bold mt-4">Congrats you finished your list!</p>
            </div>
            
            
            <!-- Danh sách các tasks -->
            <div class="mt-4 ">
                <h2 class="font-bold mb-3">Todo Task:</h2>
                <ul class="pl-5">
                    <template x-for="task in filterTask()" :key="task.id">
                        <li class="bg-slate-200 mb-3 flex justify-between h-7">
                            <div class="pl-3">
                                <input class="mr-2" type="checkbox" :checked="task.done" @change="updateTaskStatus(task)">
                                <span class="font-semibold" x-text="task.text" :class="{'line-through' : task.done}"></span>
                            </div>
                            <button class="font-light text-sm mr-2" @click="removeTask(task)">Remove.</button>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
        
        <!-- Button và nhập task -->
        <div class="mb-2">
            <input class="align-middle w-5/6 border-4  p-2" type="text" placeholder="Add to do item" 
            class="p-1 mr-10" x-model="newTask">
            <button class="align-middle  py-2" @click="addTask">
                <span class="material-symbols-outlined">
                add
                </span>
            </button>
        </div>
    </div>
</div>  

