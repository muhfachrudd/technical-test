<script setup>
import { ref, computed } from 'vue';
import MainTable from './components/MainTable.vue';
import KanbanBoard from './components/KanbanBoard.vue';
import TaskModal from './components/TaskModal.vue';
import { Search, UserCircle2, ArrowUpDown, ChevronDown, List, LayoutGrid, Home } from 'lucide-vue-next';

import { INITIAL_TASKS } from './utils/dummyData';

const activeTab = ref('table');
const showModal = ref(false);
const searchQuery = ref('');
const personFilter = ref('');
const sortConfig = ref([]);

const tasks = ref(INITIAL_TASKS);

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

      <div class="flex items-center gap-8 text-[14px] text-gray-400">
        <button class="toolbar-btn">
          <Search class="w-4 h-4" /> Search
        </button>
        <button class="toolbar-btn">
          <UserCircle2 class="w-4 h-4" /> Person
        </button>
        <button class="toolbar-btn" @click="sortConfig = []">
          <ArrowUpDown class="w-4 h-4" /> Sort
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
      <MainTable v-if="activeTab === 'table'" :tasks="filteredTasks" @update="updateTask" @sort="toggleSort" />
      
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

