import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

export const todoService = {
  async getTodos(filters = {}) {
    const response = await api.get('/todos', { params: filters });
    return response.data;
  },
  async createTodo(todo) {
    const response = await api.post('/todos', todo);
    return response.data;
  },
  async updateTodo(id, todo) {
    const response = await api.put(`/todos/${id}`, todo);
    return response.data;
  },
  async deleteTodo(id) {
    const response = await api.delete(`/todos/${id}`);
    return response.data;
  },
  async exportExcel(filters = {}) {
    return api.get('/excel', { 
      params: filters,
      responseType: 'blob'
    });
  },
  async getChartData(type) {
    const response = await api.get('/chart', { params: { type } });
    return response.data;
  }
};

export default api;
