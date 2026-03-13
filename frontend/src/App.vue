<script setup>
import { ref, computed } from 'vue';
import MainTable from './components/MainTable.vue';
import KanbanBoard from './components/KanbanBoard.vue';
import TaskModal from './components/TaskModal.vue';
import { Search, Users, SortDesc, Plus, LayoutGrid, List, ChevronDown, Home } from 'lucide-vue-next';

const activeTab = ref('table');
const showModal = ref(false);
const searchQuery = ref('');
const personFilter = ref('');
const sortConfig = ref([]);

const tasks = ref([
  { id: 1, task: 'New Task', developer: 'John', status: 'Waiting for review', priority: 'Medium', type: 'Feature Enhancements', date: '2026-03-13', estimated_sp: 0, actual_sp: 0 },
  { id: 2, task: 'New task', developer: 'Jane', status: 'In Progress', priority: 'Best Effort', type: 'Feature Enhancements', date: '2026-03-14', estimated_sp: 0, actual_sp: 0 },
  { id: 3, task: 'New Task', developer: 'Doe', status: 'In Progress', priority: 'Best Effort', type: 'Feature Enhancements', date: '2026-03-15', estimated_sp: 0, actual_sp: 0 },
  { id: 4, task: 'Committed Feature', developer: 'Jane', status: 'Ready to start', priority: 'High', type: 'Other', date: '2026-03-16', estimated_sp: 2, actual_sp: 1.5 }
]);

const addTask = (newTask) => {
  tasks.value.unshift({ id: Date.now(), ...newTask });
  showModal.value = false;
};

const updateTask = (updatedTask) => {
  const index = tasks.value.findIndex(t => t.id === updatedTask.id);
  if (index !== -1) tasks.value[index] = { ...updatedTask };
};

const developers = computed(() => Array.from(new Set(tasks.value.map(t => t.developer).filter(Boolean))));

const filteredTasks = computed(() => {
  let result = [...tasks.value];
  if (searchQuery.value) result = result.filter(t => t.task.toLowerCase().includes(searchQuery.value.toLowerCase()));
  if (personFilter.value) result = result.filter(t => t.developer === personFilter.value);
  if (sortConfig.value.length > 0) {
    result.sort((a, b) => {
      for (const { key, order } of sortConfig.value) {
        if (a[key] < b[key]) return order === 'asc' ? -1 : 1;
        if (a[key] > b[key]) return order === 'asc' ? 1 : -1;
      }
      return 0;
    });
  }
  return result;
});

const toggleSort = (key) => {
  const existing = sortConfig.value.find(s => s.key === key);
  if (!existing) sortConfig.value.push({ key, order: 'asc' });
  else if (existing.order === 'asc') existing.order = 'desc';
  else sortConfig.value = sortConfig.value.filter(s => s.key !== key);
};
</script>

<template>
  <div class="flex min-h-screen bg-[#0F0F17] text-[#E1E1E6]">
    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Tabs Header -->
      <div class="h-14 border-b border-[#232333] flex items-center px-6 gap-8">
        <button @click="activeTab = 'table'" 
                :class="['h-full flex items-center gap-2 px-2 text-[12px] font-medium border-b-2 transition-all transition-colors duration-200', 
                         activeTab === 'table' ? 'border-blue-500 text-white' : 'border-transparent text-gray-500 hover:text-gray-300']">
          <Home class="w-3.5 h-3.5" /> Main Table
        </button>
        <button @click="activeTab = 'kanban'" 
                :class="['h-full flex items-center gap-2 px-2 text-[12px] font-medium border-b-2 transition-all transition-colors duration-200', 
                         activeTab === 'kanban' ? 'border-blue-500 text-white' : 'border-transparent text-gray-500 hover:text-gray-300']">
          Kanban
        </button>
        <button class="text-gray-600 hover:text-white pb-1">+</button>
      </div>

      <!-- Toolbar -->
      <div class="p-6">
        <div class="flex items-center gap-6 mb-8">
          <div class="flex items-center">
            <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-l-md flex items-center gap-2 text-[12px] font-medium transition-colors">
              New task
            </button>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-2 rounded-r-md border-l border-white/10 transition-colors">
              <ChevronDown class="w-3.5 h-3.5" />
            </button>
          </div>

          <div class="flex items-center gap-5 text-gray-400">
            <div class="flex items-center gap-2 cursor-pointer hover:text-white transition-colors group">
              <Search class="w-4 h-4" />
              <input v-model="searchQuery" placeholder="Search" class="bg-transparent border-none text-[12px] w-24 focus:w-48 transition-all p-0">
            </div>
            <div class="flex items-center gap-2 cursor-pointer hover:text-white transition-colors">
              <Users class="w-4 h-4" />
              <select v-model="personFilter" class="bg-transparent border-none p-0 text-[12px] cursor-pointer outline-none">
                <option value="" class="bg-[#181824]">Person</option>
                <option v-for="dev in developers" :key="dev" :value="dev" class="bg-[#181824]">{{ dev }}</option>
              </select>
            </div>
            <div @click="sortConfig = []" class="flex items-center gap-2 cursor-pointer hover:text-white transition-colors">
              <SortDesc class="w-4 h-4" />
              <span class="text-[12px]">Sort</span>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-2 text-[13px] font-bold text-gray-300 mb-4 ml-2">
            <ChevronDown class="w-4 h-4 text-purple-600" /> All Task
        </div>

        <div class="transition-all duration-300 overflow-x-auto">
          <MainTable v-if="activeTab === 'table'" :tasks="filteredTasks" @update="updateTask" @sort="toggleSort" />
          <KanbanBoard v-else :tasks="filteredTasks" @update="updateTask" />
        </div>
      </div>
    </div>

    <!-- Modal -->
    <TaskModal v-if="showModal" @close="showModal = false" @save="addTask" />
  </div>
</template>
