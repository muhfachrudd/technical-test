<script setup>
import { computed } from "vue";
import draggable from "vuedraggable";
import { MessageSquare, MoreHorizontal } from "lucide-vue-next";

const props = defineProps(["tasks"]);
const emit = defineEmits(["update"]);

const columns = [
  "Ready to start",
  "In Progress",
  "Waiting for review",
  "Pending Deploy",
  "Done",
  "Stuck",
];

const columnColors = {
  "Ready to start": "#2563EB",
  "In Progress": "#F59E0B",
  "Waiting for review": "#06B6D4",
  "Pending Deploy": "#8B5CF6",
  Done: "#10B981",
  Stuck: "#EF4444",
};

const tasksByStatus = computed(() => {
  const map = {};
  columns.forEach((col) => {
    map[col] = props.tasks.filter((t) => t.status === col);
  });
  return map;
});

const onDragEnd = (status) => {
  const list = tasksByStatus.value[status];
  list.forEach((task) => {
    if (task.status !== status) {
      emit("update", { ...task, status });
    }
  });
};

const moveTask = (evt, newStatus) => {
  if (evt.added) {
    const task = evt.added.element;
    emit("update", { ...task, status: newStatus });
  }
};

const getPriorityColor = (priority) => {
  const map = {
    Critical: "#EF4444",
    High: "#F97316",
    Medium: "#3B82F6",
    Low: "#6B7280",
    "Best Effort": "#94A3B8",
  };
  return map[priority] || "#6B7280";
};

const getTypeColor = (type) => {
  const map = {
    "Feature Enhancements": "#EC4899",
    Other: "#6366F1",
    Bug: "#F43F5E",
  };
  return map[type] || "#6B7280";
};
</script>

<template>
  <div class="flex gap-4 overflow-x-auto pb-8 min-h-[600px]">
    <div
      v-for="col in columns"
      :key="col"
      class="flex-shrink-0 w-[300px] flex flex-col gap-4"
    >
      <!-- Column Header -->
      <div
        class="flex items-center justify-between p-3 rounded-t-lg border-b-4"
        :style="{
          borderColor: columnColors[col],
          backgroundColor: columnColors[col] + '11',
        }"
      >
        <div class="flex items-center gap-2">
          <span class="text-sm font-semibold text-white">{{ col }}</span>
          <span
            class="text-xs text-gray-500 bg-black/30 px-2 py-0.5 rounded-full"
          >
            {{ tasksByStatus[col].length }}
          </span>
        </div>
        <button class="text-gray-500 hover:text-white">
          <MoreHorizontal class="w-4 h-4" />
        </button>
      </div>

      <!-- Draggable Area -->
      <draggable
        :list="tasksByStatus[col]"
        group="tasks"
        item-key="id"
        class="flex-1 flex flex-col gap-3 p-1 min-h-[100px]"
        ghost-class="opacity-50"
        @change="(e) => moveTask(e, col)"
      >
        <template #item="{ element: task }">
          <div
            class="bg-[#1E1E2D] p-4 rounded-xl border border-gray-800 shadow-lg cursor-grab active:cursor-grabbing hover:border-gray-600 transition-all group"
          >
            <div class="flex items-start justify-between mb-3">
              <h3 class="text-sm font-medium text-gray-200 line-clamp-2">
                {{ task.task }}
              </h3>
            </div>

            <div class="flex flex-wrap gap-2 mb-4">
              <span
                class="px-2 py-0.5 rounded text-[10px] font-bold"
                :style="{
                  backgroundColor: getPriorityColor(task.priority) + '22',
                  color: getPriorityColor(task.priority),
                }"
              >
                {{ task.priority }}
              </span>
              <span
                class="text-[10px] text-gray-500 bg-gray-800 px-2 py-0.5 rounded"
              >
                {{ task.estimated_sp }} SP
              </span>
            </div>

            <div class="flex items-center border-t border-gray-800/50 pt-3">
              <div
                class="h-4 w-1 rounded-full mr-2"
                :style="{ backgroundColor: getTypeColor(task.type) }"
              ></div>
              <span class="text-[10px] text-gray-400 truncate flex-1">{{
                task.type
              }}</span>

              <div class="flex items-center gap-2 ml-auto">
                <MessageSquare class="w-3.5 h-3.5 text-gray-600" />
                <div
                  class="w-6 h-6 rounded-full bg-gray-700 flex items-center justify-center text-[9px] font-bold border border-gray-600"
                >
                  {{ task.developer?.[0]?.toUpperCase() || "?" }}
                </div>
              </div>
            </div>
          </div>
        </template>
      </draggable>
    </div>
  </div>
</template>
