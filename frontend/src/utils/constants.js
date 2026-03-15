export const STATUSES = {
  READY_TO_START: "Ready to start",
  IN_PROGRESS: "In Progress",
  WAITING_FOR_REVIEW: "Waiting for review",
  DONE: "Done",
  PENDING_DEPLOY: "Pending Deploy",
  STUCK: "Stuck",
};

export const COLOR_PALETTE = {
  BLUE: "#2D7FF9",
  ORANGE: "#F9B249",
  CYAN: "#9FE3F0",
  GREEN: "#34D399",
  PURPLE: "#A855F7",
  PINK: "#F472B6",
  INDIGO: "#818CF8",
  RED: "#EF4444",
  GRAY: "#6B7280",
  GRAY_LIGHT: "#94A3B8",
};

export const KANBAN_COLUMNS = [
  STATUSES.READY_TO_START,
  STATUSES.IN_PROGRESS,
  STATUSES.WAITING_FOR_REVIEW,
  STATUSES.DONE,
];

export const COLUMN_COLORS = {
  [STATUSES.READY_TO_START]: COLOR_PALETTE.BLUE,
  [STATUSES.IN_PROGRESS]: COLOR_PALETTE.ORANGE,
  [STATUSES.WAITING_FOR_REVIEW]: COLOR_PALETTE.CYAN,
  [STATUSES.DONE]: COLOR_PALETTE.GREEN,
};

export const STATUS_OPTIONS = [
  { name: STATUSES.WAITING_FOR_REVIEW, color: COLOR_PALETTE.CYAN, textColor: "#FFFFFF" },
  { name: STATUSES.IN_PROGRESS, color: COLOR_PALETTE.ORANGE, textColor: "#FFFFFF" },
  { name: STATUSES.READY_TO_START, color: COLOR_PALETTE.BLUE, textColor: "#FFFFFF" },
  { name: STATUSES.PENDING_DEPLOY, color: COLOR_PALETTE.PURPLE, textColor: "#FFFFFF" },
  { name: STATUSES.DONE, color: COLOR_PALETTE.GREEN, textColor: "#FFFFFF" },
  { name: STATUSES.STUCK, color: COLOR_PALETTE.RED, textColor: "#FFFFFF" },
];

export const PRIORITY_OPTIONS = [
  { name: 'Critical', color: COLOR_PALETTE.RED },
  { name: 'High', color: COLOR_PALETTE.PURPLE },
  { name: 'Medium', color: COLOR_PALETTE.BLUE },
  { name: 'Low', color: COLOR_PALETTE.GRAY },
  { name: 'Best Effort', color: COLOR_PALETTE.ORANGE },
];

export const TYPE_OPTIONS = [
  { name: 'Feature Enhancement', color: COLOR_PALETTE.PINK, textColor: "#FFFFFF" },
  { name: 'Feature Enhancements', color: COLOR_PALETTE.PINK, textColor: "#FFFFFF" }, // Matching both for compatibility
  { name: 'Other', color: COLOR_PALETTE.INDIGO, textColor: "#FFFFFF" },
  { name: 'Bug', color: COLOR_PALETTE.RED, textColor: "#FFFFFF" }
];

// Helper functions for logic separation
export const getPriorityColor = (priority) => {
  const found = PRIORITY_OPTIONS.find(p => p.name === priority);
  return found ? found.color : COLOR_PALETTE.GRAY;
};

export const getTypeColor = (type) => {
  const found = TYPE_OPTIONS.find(t => t.name === type);
  return found ? found.color : COLOR_PALETTE.GRAY;
};
