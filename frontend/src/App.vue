<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import MainTable from './components/MainTable.vue';
import KanbanBoard from './components/KanbanBoard.vue';
import TaskModal from './components/TaskModal.vue';
import { Search, UserCircle2, ArrowUpDown, ChevronDown, List, LayoutGrid, Home, Download } from 'lucide-vue-next';

import { todoService } from './services/api';

const activeTab = ref('table');
const showModal = ref(false);
const searchQuery = ref('');
const personFilter = ref('');
const sortConfig = ref([]);
const tasks = ref([]);
const isLoading = ref(false);

const mapToBackend = (task) => ({
  title: task.task,
  assignee: task.developer,
  due_date: task.date || new Date().toISOString().split('T')[0],
  status: task.status === 'In Progress' ? 'in_progress' : 
          task.status === 'Waiting for review' ? 'open' : 
          task.status === 'Done' ? 'completed' : 'pending',
  priority: task.priority?.toLowerCase() === 'high' ? 'high' : 
            task.priority?.toLowerCase() === 'medium' ? 'medium' : 'low',
  time_tracked: task.actual_sp || 0
});

const mapToFrontend = (apiTask) => ({
  id: apiTask.id,
  task: apiTask.title,
  developer: apiTask.assignee,
  date: apiTask.due_date,
  status: apiTask.status === 'in_progress' ? 'In Progress' : 
          apiTask.status === 'open' ? 'Waiting for review' : 
          apiTask.status === 'completed' ? 'Done' : 'Ready to start',
  priority: apiTask.priority === 'high' ? 'High' : 
            apiTask.priority === 'medium' ? 'Medium' : 'Low',
  actual_sp: apiTask.time_tracked,
  estimated_sp: apiTask.estimated_sp || 0,
  type: apiTask.type || 'Other'
});

const fetchTasks = async () => {
  isLoading.value = true;
  try {
    const params = {};
    if (searchQuery.value) params.title = searchQuery.value;
    if (personFilter.value) params.assignee = personFilter.value;
    
    const data = await todoService.getTodos(params);
    tasks.value = data.map(mapToFrontend);
  } catch (error) {
    console.error("Failed to fetch tasks:", error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchTasks);

// Re-fetch when filters change (Backend filtering requirement)
watch([searchQuery, personFilter], fetchTasks);

const addTask = async (newTask) => {
  try {
    const backendTask = mapToBackend(newTask);
    const { data } = await todoService.createTodo(backendTask);
    tasks.value.unshift(mapToFrontend(data));
    showModal.value = false;
  } catch (error) {
    console.error("Failed to add task:", error);
  }
};

const updateTask = async (updatedTask) => {
  try {
    const backendTask = mapToBackend(updatedTask);
    await todoService.updateTodo(updatedTask.id, backendTask);
    const index = tasks.value.findIndex(t => t.id === updatedTask.id);
    if (index !== -1) tasks.value[index] = { ...updatedTask };
  } catch (error) {
    console.error("Failed to update task:", error);
  }
};

const exportData = async () => {
  try {
    const response = await todoService.exportExcel();
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'todos.xlsx');
    document.body.appendChild(link);
    link.click();
  } catch (error) {
    console.error("Failed to export excel:", error);
  }
};

const developers = computed(() => {
  const allNames = tasks.value.flatMap(t => 
    t.developer ? t.developer.split(',').map(n => n.trim()) : []
  );
  return Array.from(new Set(allNames)).sort();
});

const filteredTasks = computed(() => {
  let result = [...tasks.value];
  if (searchQuery.value) result = result.filter(t => t.task.toLowerCase().includes(searchQuery.value.toLowerCase()));
  if (personFilter.value) result = result.filter(t => t.developer && t.developer.includes(personFilter.value));
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
  <div class="app-layout">
    <!-- TABS -->
    <div class="tabs px-6 border-b border-[#232333] mb-4">
      <button @click="activeTab = 'table'" 
              :class="['tab', { active: activeTab === 'table' }]">
        <Home class="w-4 h-4" /> Main Table
      </button>
      <button @click="activeTab = 'kanban'" 
              :class="['tab', { active: activeTab === 'kanban' }]">
        Kanban
      </button>
    </div>

    <!-- TOOLBAR -->
    <div class="toolbar px-6 py-2 flex items-center gap-6 mb-2">
      <div class="btn-new-group">
        <button class="btn-new" @click="showModal = true">New task</button>
        <button class="btn-new-caret"><ChevronDown class="w-4 h-4" /></button>
      </div>

      <div class="flex items-center gap-6 text-[14px] text-gray-400">
        <!-- Search Input -->
        <div class="flex items-center gap-2 bg-[#1E1E2D] px-3 py-1 rounded border border-gray-800 focus-within:border-blue-500 transition-colors">
          <Search class="w-4 h-4" />
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search tasks..." 
            class="bg-transparent border-none outline-none text-white text-[13px] w-32"
          />
        </div>

        <!-- Person Filter Dropdown -->
        <div class="flex items-center gap-2 group relative">
          <UserCircle2 class="w-4 h-4 cursor-pointer" />
          <select 
            v-model="personFilter" 
            class="bg-transparent border-none outline-none text-gray-400 hover:text-white cursor-pointer text-[13px] appearance-none"
          >
            <option value="" class="bg-[#1E1E2D]">Person: All</option>
            <option v-for="dev in developers" :key="dev" :value="dev" class="bg-[#1E1E2D]">
              {{ dev }}
            </option>
          </select>
        </div>

        <!-- Sort Toggle -->
        <button 
          @click="toggleSort('task')" 
          :class="['toolbar-btn flex items-center gap-2', { 'text-blue-400': sortConfig.length > 0 }]"
        >
          <ArrowUpDown class="w-4 h-4" />
          <span>Sort: {{ sortConfig.length > 0 ? (sortConfig[0].order === 'asc' ? 'A-Z' : 'Z-A') : 'None' }}</span>
        </button>

        <!-- Export button -->
        <button @click="exportData" class="toolbar-btn text-green-400 hover:text-green-300">
          <Download class="w-4 h-4" />
          <span>Export Excel</span>
        </button>

        <!-- Color Swatches (Only in Kanban Mode) -->
        <div v-if="activeTab === 'kanban'" class="flex items-center gap-0.5 ml-2">
          <div class="w-10 h-4 rounded-s" style="background-color: #2D7FF9;"></div>
          <div class="w-10 h-4" style="background-color: #F9B249;"></div>
          <div class="w-10 h-4 rounded-e" style="background-color: #9FE3F0;"></div>
        </div>
      </div>
    </div>


    <!-- MAIN CONTENT -->
    <div class="content">
      <MainTable v-if="activeTab === 'table'" :tasks="filteredTasks" :sortConfig="sortConfig" @update="updateTask" @sort="toggleSort" />
      
      <div v-else class="placeholder-view px-6">
        <KanbanBoard :tasks="filteredTasks" @update="updateTask" />
      </div>
    </div>

    <!-- MODAL -->
    <TaskModal v-if="showModal" @close="showModal = false" @save="addTask" />
  </div>
</template>

<style>
.app-layout {
  min-height: 100vh;
  background: #0D0D14;
  color: #C8C8D8;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* ── TABS ── */
.tabs {
  display: flex;
  align-items: center;
  gap: 12px;
  height: 48px;
}
.tab {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0 16px;
  height: 100%;
  font-size: 13px;
  color: #8E8E9F;
  border: none;
  background: none;
  border-bottom: 2px solid transparent;
  cursor: pointer;
  transition: all .2s;
}
.tab.active {
  color: #fff;
  border-bottom-color: #2D7FF9;
  border-bottom-width: 2px;
}
.tab:hover:not(.active) {
  color: #fff;
}
.tab-add {
  color: #555570;
  font-size: 20px;
  background: none;
  border: none;
  cursor: pointer;
}

/* ── TOOLBAR ── */
.btn-new-group {
  display: flex;
  height: 32px;
  border-radius: 4px;
  overflow: hidden;
}
.btn-new {
  background: #2563EB;
  color: #fff;
  border: none;
  padding: 0 16px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  border-right: 1px solid rgba(0,0,0,0.1);
}
.btn-new:hover { background: #1D4ED8; }
.btn-new-caret {
  background: #2563EB;
  color: #fff;
  border: none;
  padding: 0 8px;
  cursor: pointer;
}
.btn-new-caret:hover { background: #1D4ED8; }

.toolbar-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: none;
  border: none;
  color: inherit;
  font-size: 13px;
  cursor: pointer;
}
.toolbar-btn:hover { color: #fff; }

.content {
  padding-top: 0;
}
</style>

