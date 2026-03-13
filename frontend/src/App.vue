<script setup>
import { ref, computed } from 'vue';
import MainTable from './components/MainTable.vue';
import KanbanBoard from './components/KanbanBoard.vue';
import TaskModal from './components/TaskModal.vue';
import { Search, Users, SortDesc, Plus, LayoutGrid, List } from 'lucide-vue-next';

const activeTab = ref('table'); // 'table' or 'kanban'
const showModal = ref(false);
const searchQuery = ref('');
const personFilter = ref('');
const sortConfig = ref([]);

const tasks = ref([
  {
    id: 1,
    task: 'New Task',
    developer: 'John',
    status: 'Waiting for review',
    priority: 'Medium',
    type: 'Feature Enhancements',
    date: '2026-03-13',
    estimated_sp: 0,
    actual_sp: 0
  },
  {
    id: 2,
    task: 'Committed Feature',
    developer: 'Jane',
    status: 'Ready to start',
    priority: 'High',
    type: 'Other',
    date: '2026-03-14',
    estimated_sp: 2,
    actual_sp: 1.5
  },
  {
    id: 3,
    task: 'Fix Bug #12',
    developer: 'Doe',
    status: 'In Progress',
    priority: 'Critical',
    type: 'Bug',
    date: '2026-03-12',
    estimated_sp: 3,
    actual_sp: 2
  }
]);

const addTask = (newTask) => {
  tasks.value.unshift({
    id: Date.now(),
    ...newTask
  });
  showModal.value = false;
};

const updateTask = (updatedTask) => {
  const index = tasks.value.findIndex(t => t.id === updatedTask.id);
  if (index !== -1) {
    tasks.value[index] = { ...updatedTask };
  }
};

const developers = computed(() => {
  const devs = new Set();
  tasks.value.forEach(t => {
    if (t.developer) devs.add(t.developer);
  });
  return Array.from(devs);
});

const filteredTasks = computed(() => {
  let result = [...tasks.value];
  
  if (searchQuery.value) {
    result = result.filter(t => t.task.toLowerCase().includes(searchQuery.value.toLowerCase()));
  }
  
  if (personFilter.value) {
    result = result.filter(t => t.developer === personFilter.value);
  }
  
  return result;
});
</script>

<template>
  <div class="min-h-screen p-6">
    <!-- Header with Tabs -->
    <header class="flex items-center justify-between mb-8 border-b border-gray-800 pb-4">
      <div class="flex gap-8">
        <button 
          @click="activeTab = 'table'"
          :class="['flex items-center gap-2 px-4 py-2 text-sm font-medium transition-colors', activeTab === 'table' ? 'text-blue-500 border-b-2 border-blue-500' : 'text-gray-400 hover:text-white']"
        >
          <List class="w-4 h-4" />
          Main Table
        </button>
        <button 
          @click="activeTab = 'kanban'"
          :class="['flex items-center gap-2 px-4 py-2 text-sm font-medium transition-colors', activeTab === 'kanban' ? 'text-blue-500 border-b-2 border-blue-500' : 'text-gray-400 hover:text-white']"
        >
          <LayoutGrid class="w-4 h-4" />
          Kanban
        </button>
      </div>
    </header>

    <!-- Toolbar -->
    <div class="flex items-center gap-4 mb-6">
      <button 
        @click="showModal = true"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center gap-2 text-sm transition-all shadow-lg"
      >
        <Plus class="w-4 h-4" />
        New task
      </button>

      <div class="flex items-center gap-4 ml-auto">
        <div class="relative">
          <Search class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Search" 
            class="bg-gray-800 border border-transparent focus:border-blue-500 rounded-md pl-10 pr-4 py-1.5 text-sm w-64"
          >
        </div>

        <div class="flex items-center gap-2 text-gray-400">
          <Users class="w-4 h-4" />
          <select 
            v-model="personFilter"
            class="bg-transparent border-none text-sm focus:ring-0 cursor-pointer hover:text-white"
          >
            <option value="">Person</option>
            <option v-for="dev in developers" :key="dev" :value="dev">{{ dev }}</option>
          </select>
        </div>

        <button class="flex items-center gap-2 text-gray-400 hover:text-white text-sm">
          <SortDesc class="w-4 h-4" />
          Sort
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="transition-all duration-300">
      <MainTable 
        v-if="activeTab === 'table'" 
        :tasks="filteredTasks"
        @update="updateTask"
      />
      <KanbanBoard 
        v-else 
        :tasks="filteredTasks"
        @update="updateTask"
      />
    </div>

    <!-- Modal for New Task -->
    <TaskModal 
      v-if="showModal" 
      @close="showModal = false" 
      @save="addTask" 
    />
  </div>
</template>

<style>
/* Global styles in App.vue if needed, but index.css covers most */
</style>
