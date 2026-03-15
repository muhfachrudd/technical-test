<script setup>
import { computed } from "vue";
import draggable from "vuedraggable";
import { MessageSquare, GitBranch, Calendar } from "lucide-vue-next";
import { format } from "date-fns";
import { KANBAN_COLUMNS, COLUMN_COLORS, getPriorityColor, getTypeColor } from "../utils/constants";

const props = defineProps(["tasks"]);
const emit = defineEmits(["update"]);

const columns = KANBAN_COLUMNS;
const columnColors = COLUMN_COLORS;

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  try {
    return format(new Date(dateStr), "dd MMM, yyyy");
  } catch (e) {
    return dateStr;
  }
};

const tasksByStatus = computed(() => {
  const map = {};
  columns.forEach((col) => {
    map[col] = props.tasks.filter((t) => t.status === col);
  });
  return map;
});

const moveTask = (evt, newStatus) => {
  if (evt.added) {
    const task = evt.added.element;
    emit("update", { ...task, status: newStatus });
  }
};
</script>

<template>
  <div class="flex gap-4 overflow-x-auto pb-8 border-t border-[#232333] pt-4 min-h-[700px]">
    <div
      v-for="col in columns"
      :key="col"
      class="flex-shrink-0 w-[320px] flex flex-col"
    >
      <!-- Column Header -->
      <div
        class="flex items-center p-3 rounded-t-lg"
        :style="{ backgroundColor: columnColors[col] }"
      >
        <div class="flex items-center gap-2">
          <span class="text-sm font-semibold text-white">{{ col }}</span>
          <span class="text-sm text-white/90">{{ tasksByStatus[col].length }}</span>
        </div>
      </div>

      <!-- Draggable Area / Column Body -->
      <div class="bg-[#2D2D3F]/50 flex-1 rounded-b-lg">
        <draggable
          :list="tasksByStatus[col]"
          group="tasks"
          item-key="id"
          class="flex flex-col gap-3 p-3 min-h-[200px]"
          ghost-class="opacity-50"
          @change="(e) => moveTask(e, col)"
        >
          <template #item="{ element: task }">
            <div
              class="bg-[#1E1E2D] p-4 rounded-lg border border-transparent shadow-md cursor-grab active:cursor-grabbing hover:border-gray-700/50 transition-all"
            >
              <!-- Task Title -->
              <h3 class="text-[13px] font-medium text-gray-200 mb-2">
                {{ task.task }}
              </h3>

              <!-- Badges Row -->
              <div class="flex flex-wrap gap-2 mb-3">
                <!-- Priority Badge -->
                <div v-if="task.priority" class="badge-custom" :style="{ '--border-color': getPriorityColor(task.priority) }">
                  {{ task.priority }}
                </div>
                
                <!-- SP Badge -->
                <div class="badge-custom" style="--border-color: #9FE3F0">
                  {{ task.estimated_sp }} SP
                </div>

                <!-- Date Badge -->
                <div class="badge-custom flex items-center gap-1" style="--border-color: #6B7280">
                  <Calendar class="w-3 h-3" />
                  {{ formatDate(task.date) }}
                </div>
              </div>

              <!-- Full-width Type Label -->
              <div v-if="task.type" 
                   class="badge-custom mb-3 block w-fit" 
                   :style="{ '--border-color': getTypeColor(task.type) }">
                {{ task.type }}
              </div>

              <!-- Footer -->
              <div class="flex items-center justify-between pt-1">
                <div class="flex -space-x-2">
                  <div v-for="dev in (task.developer ? task.developer.split(',') : [])" :key="dev" 
                       class="w-7 h-7 rounded-full bg-gray-600 overflow-hidden border border-gray-700 flex items-center justify-center text-[10px] text-white bg-indigo-500 uppercase">
                    {{ dev.trim()[0] }}
                  </div>
                </div>

                <div class="flex items-center gap-3 text-gray-500">
                  <MessageSquare class="w-4 h-4" />
                  <GitBranch class="w-4 h-4" />
                </div>
              </div>
            </div>
          </template>
        </draggable>
      </div>
    </div>
  </div>
</template>

<style scoped>
@reference "../index.css";

.badge-custom {
  @apply bg-[#2A2A3C] text-gray-300 text-[11px] px-2 py-1 rounded border-l-[3px];
  border-left-color: var(--border-color);
}
</style>

