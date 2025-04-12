<template>
    <div class="queue-manager">
        <div class="card">
            <div class="card-header">
                <h3>Управление очередями</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <button @click="getStatus" class="btn btn-info">Обновить статус</button>
                </div>
                
                <div v-if="workers.length" class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>PID</th>
                                <th>Очередь</th>
                                <th>Время работы</th>
                                <th>CPU %</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="worker in workers" :key="worker.pid">
                                <td>{{ worker.pid }}</td>
                                <td>{{ worker.queue }}</td>
                                <td>{{ worker.uptime }}</td>
                                <td>{{ worker.cpu }}%</td>
                                <td>
                                    <button @click="stopWorker(worker)" class="btn btn-warning btn-sm">Остановить</button>
                                    <button @click="clearQueue(worker)" class="btn btn-danger btn-sm ms-2">Очистить очередь</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div v-else class="alert alert-info">
                    Нет активных воркеров
                </div>
                
                <div class="mt-3">
                    <div class="form-group">
                        <label for="queue">Очередь:</label>
                        <select v-model="queue" class="form-control" id="queue">
                            <option value="high">Высокий приоритет</option>
                            <option value="email">Email</option>
                            <option value="default">По умолчанию</option>
                            <option value="low">Низкий приоритет</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tries">Количество попыток:</label>
                        <input type="number" v-model="tries" class="form-control" id="tries">
                    </div>
                    <div class="form-group">
                        <label for="timeout">Таймаут (сек):</label>
                        <input type="number" v-model="timeout" class="form-control" id="timeout">
                    </div>
                    <button @click="startWorker" class="btn btn-success">Запустить воркер</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            workers: [],
            queue: 'default',
            tries: 3,
            timeout: 60
        }
    },
    
    mounted() {
        this.getStatus();
        // Обновляем статус каждые 5 секунд
        setInterval(this.getStatus, 5000);
    },
    
    methods: {
        async getStatus() {
            try {
                const response = await axios.get('/admin/queue/status');
                this.workers = response.data.workers;
            } catch (error) {
                console.error('Ошибка получения статуса:', error);
            }
        },
        
        async startWorker() {
            try {
                await axios.post('/admin/queue/start', {
                    queue: this.queue,
                    tries: this.tries,
                    timeout: this.timeout
                });
                this.getStatus();
            } catch (error) {
                console.error('Ошибка запуска воркера:', error);
            }
        },
        
        async stopWorker(worker) {
            try {
                await axios.post('/admin/queue/stop', {
                    queue: worker.queue
                });
                this.getStatus();
            } catch (error) {
                console.error('Ошибка остановки воркера:', error);
            }
        },
        
        async clearQueue(worker) {
            try {
                await axios.post('/admin/queue/clear', {
                    queue: worker.queue
                });
                this.getStatus();
            } catch (error) {
                console.error('Ошибка очистки очереди:', error);
            }
        }
    }
}
</script>

<style scoped>
.queue-manager {
    max-width: 800px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: 1rem;
}
</style> 