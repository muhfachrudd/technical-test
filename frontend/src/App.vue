<script setup>
import { ref, computed } from 'vue';
import MainTable from './components/MainTable.vue';
import KanbanBoard from './components/KanbanBoard.vue';
import TaskModal from './components/TaskModal.vue';
import { Search, Users, SortDesc, ChevronDown, List, LayoutGrid, Home } from 'lucide-vue-next';

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
  <div class="app-layout">
    <!-- TABS -->
    <div class="tabs">
      <button @click="activeTab = 'table'" 
              :class="['tab', { active: activeTab === 'table' }]">
        <Home class="tab-icon" /> Main Table
      </button>
      <button @click="activeTab = 'kanban'" 
              :class="['tab', { active: activeTab === 'kanban' }]">
        Kanban
      </button>
      <button class="tab-add">+</button>
    </div>

    <!-- TOOLBAR -->
    <div class="toolbar">
      <div class="btn-new">
        <button class="btn-new-main" @click="showModal = true">New task</button>
        <button class="btn-new-caret"><ChevronDown class="icon-caret" /></button>
      </div>

      <div class="toolbar-sep"></div>

      <div class="tb-action">
        <Search class="icon-action" />
        <input v-model="searchQuery" placeholder="Search" class="search-input" />
      </div>

      <div class="tb-action">
        <Users class="icon-action" />
        <select v-model="personFilter" class="dev-select">
          <option value="">Person</option>
          <option v-for="dev in developers" :key="dev" :value="dev">{{ dev }}</option>
        </select>
        <ChevronDown class="icon-mini" />
      </div>

      <button class="tb-action" @click="sortConfig = []">
        <SortDesc class="icon-action" /> Sort
      </button>
    </div>

    <!-- MAIN CONTENT -->
    <div class="content">
      <div class="section-label">
        <ChevronDown class="icon-section" /> All Task
      </div>

      <MainTable v-if="activeTab === 'table'" :tasks="filteredTasks" @update="updateTask" @sort="toggleSort" />
      
      <div v-else class="placeholder-view">
        <KanbanBoard :tasks="filteredTasks" @update="updateTask" />
      </div>
    </div>

    <!-- MODAL -->
    <TaskModal v-if="showModal" @close="showModal = false" @save="addTask" />
  </div>
</template>

<style>
/* Global resets for the app specifically */
.app-layout {
  min-height: 100vh;
  background: #0D0D14;
  color: #C8C8D8;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* ── TABS ── */
.tabs {
  display: flex;
  align-items: flex-end;
  padding: 0 20px;
  border-bottom: 1px solid #1E1E2E;
  height: 50px;
}
.tab {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 0 14px;
  height: 100%;
  font-size: 12px;
  font-weight: 500;
  color: #555570;
  border: none;
  background: none;
  border-bottom: 2.5px solid transparent;
  cursor: pointer;
  transition: color .15s;
}
.tab.active {
  color: #E0E0F0;
  border-bottom-color: #3A86FF;
}
.tab:hover:not(.active) {
  color: #9090B0;
}
.tab-icon {
  width: 13px;
  height: 13px;
}
.tab-add {
  color: #3A3A55;
  font-size: 18px;
  padding: 0 10px 4px;
  cursor: pointer;
  background: none;
  border: none;
}

/* ── TOOLBAR ── */
.toolbar {
  display: flex;
  align-items: center;
  gap: 24px;
  padding: 14px 20px 12px;
}
.btn-new {
  display: flex;
  align-items: center;
  height: 32px;
  border-radius: 6px;
  overflow: hidden;
}
.btn-new-main {
  background: #2563EB;
  color: #fff;
  border: none;
  padding: 0 16px;
  height: 100%;
  cursor: pointer;
  font-size: 12px;
  font-weight: 600;
  border-right: 1px solid rgba(255,255,255,.18);
}
.btn-new-caret {
  background: #2563EB;
  color: #fff;
  border: none;
  padding: 0 9px;
  height: 100%;
  cursor: pointer;
}
.icon-caret {
  width: 12px;
  height: 12px;
}
.toolbar-sep {
  width: 1px;
  height: 20px;
  background: #1E1E2E;
}
.tb-action {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #8080A0;
  font-size: 12px;
  cursor: pointer;
  background: none;
  border: none;
  padding: 0;
}
.tb-action:hover {
  color: #C0C0D8;
}
.icon-action {
  width: 14px;
  height: 14px;
}
.search-input {
  background: transparent;
  border: none;
  outline: none;
  color: inherit;
  font-size: 12px;
  width: 80px;
  transition: width .2s;
}
.search-input:focus {
  width: 140px;
}
.dev-select {
  background: transparent;
  border: none;
  outline: none;
  color: inherit;
  font-size: 12px;
  cursor: pointer;
  appearance: none;
}
.icon-mini {
  width: 11px;
  height: 11px;
  opacity: .5;
}

/* ── CONTENT ── */
.content {
  padding: 0 16px 16px;
}
.section-label {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 4px 8px;
  font-size: 12.5px;
  font-weight: 700;
  color: #B0B0C8;
}
.icon-section {
  width: 14px;
  height: 14px;
  color: #7C3AED;
}
.placeholder-view {
  padding: 20px 0;
}
</style>
