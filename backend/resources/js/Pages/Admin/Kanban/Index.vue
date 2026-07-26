<template>
  <AdminLayout title="Kanban Board">
    <template #actions>
      <button @click="showCreateModal = true" class="btn-primary text-sm">
        + New Card
      </button>
    </template>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
      <div v-for="stage in stages" :key="stage" class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-semibold text-gray-900 dark:text-gray-100 uppercase text-sm">
            {{ stage }}
            <span class="ml-2 px-2 py-0.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded">
              {{ localCards[stage]?.length || 0 }}
            </span>
          </h3>
        </div>

        <VueDraggable
          v-model="localCards[stage]"
          :group="{ name: 'cards', pull: true, put: canDropInStage(stage) }"
          :animation="200"
          class="space-y-3 min-h-[200px]"
          @end="onDragEnd">
          <div v-for="card in localCards[stage]" :key="card.id" class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-3 cursor-move hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-2">
              <h4 class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ card.title }}</h4>
              <span class="px-2 py-0.5 bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 text-xs rounded">
                {{ formatType(card.type) }}
              </span>
            </div>

            <p v-if="card.description" class="text-xs text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">
              {{ card.description }}
            </p>

            <div v-if="card.members?.length" class="flex flex-wrap gap-1 mb-2">
              <span v-for="member in card.members.slice(0, 3)" :key="member.id"
                class="px-1.5 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs rounded">
                {{ member.nameEnglish }}
              </span>
              <span v-if="card.members.length > 3" class="text-xs text-gray-500 dark:text-gray-400">
                +{{ card.members.length - 3 }}
              </span>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-800">
              <span v-if="card.dueDate" class="text-xs text-gray-500 dark:text-gray-400">
                Due: {{ formatDate(card.dueDate) }}
              </span>
              <div class="flex gap-2">
                <button v-if="stage === 'planning'" @click="confirmCard(card)" class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300 text-xs font-medium">
                  Confirm
                </button>
                <button @click="editCard(card)" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 text-xs">
                  Edit
                </button>
                <button @click="deleteCard(card)" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-xs">
                  Delete
                </button>
              </div>
            </div>
          </div>
        </VueDraggable>
      </div>
    </div>

    <!-- Confirm Modal -->
    <ConfirmKanbanModal
      v-if="cardToConfirm"
      :card="cardToConfirm"
      @close="cardToConfirm = null"
      @confirmed="cardToConfirm = null" />

    <!-- Create Modal -->
    <teleport to="body">
      <div v-if="showCreateModal" class="fixed inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center z-50" @click="showCreateModal = false">
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 max-w-lg w-full mx-4 shadow-xl" @click.stop>
          <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Create Kanban Card</h3>
          <form @submit.prevent="createCard" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
              <input v-model="newCard.title" type="text" class="input" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
              <select v-model="newCard.type" class="input" required>
                <option value="artist_performance">Artist Performance</option>
                <option value="artist_appearance">Artist Appearance</option>
                <option value="content_filming">Content Filming</option>
                <option value="practice_day">Practice Day</option>
                <option value="social_media_post">Social Media Post</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
              <textarea v-model="newCard.description" class="input" rows="3"></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stage</label>
              <select v-model="newCard.stage" class="input">
                <option value="backlog">Backlog</option>
                <option value="planning">Planning</option>
                <option value="completed">Completed</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date (Optional)</label>
              <DateInput v-model="newCard.due_date" />
            </div>
            <div class="flex gap-3">
              <button type="submit" class="btn-primary flex-1">Create</button>
              <button type="button" @click="showCreateModal = false" class="btn-secondary flex-1">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </teleport>

    <!-- Edit Modal -->
    <teleport to="body">
      <div v-if="showEditModal && cardToEdit" class="fixed inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center z-50" @click="showEditModal = false">
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 max-w-lg w-full mx-4 shadow-xl" @click.stop>
          <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Edit Kanban Card</h3>
          <form @submit.prevent="updateCard" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
              <input v-model="cardToEdit.title" type="text" class="input" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
              <textarea v-model="cardToEdit.description" class="input" rows="3"></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date (Optional)</label>
              <DateInput v-model="cardToEdit.dueDate" />
            </div>
            <div class="flex gap-3">
              <button type="submit" class="btn-primary flex-1">Update</button>
              <button type="button" @click="showEditModal = false; cardToEdit = null" class="btn-secondary flex-1">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </teleport>

    <!-- Delete Confirmation Modal -->
    <teleport to="body">
      <div v-if="cardToDelete" class="fixed inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center z-50" @click="cardToDelete = null">
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 max-w-md w-full mx-4 shadow-xl" @click.stop>
          <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Delete Card</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            Are you sure you want to delete "<strong class="text-gray-900 dark:text-gray-100">{{ cardToDelete.title }}</strong>"? This action cannot be undone.
          </p>
          <div class="flex gap-3">
            <button @click="confirmDelete" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
              Delete
            </button>
            <button @click="cardToDelete = null" class="btn-secondary flex-1">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </teleport>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DateInput from '@/Components/Admin/DateInput.vue'
import { VueDraggable } from 'vue-draggable-plus'
import ConfirmKanbanModal from '@/Components/Admin/ConfirmKanbanModal.vue'

const props = defineProps({
  cards: Object,
  stages: Array
})

// Create a local mutable copy of cards for VueDraggable
const localCards = ref({
  backlog: [],
  planning: [],
  confirmed: [],
  completed: []
})

// Watch for props changes and update local copy
watch(() => props.cards, (newCards) => {
  if (newCards) {
    localCards.value = {
      backlog: [...(newCards.backlog || [])],
      planning: [...(newCards.planning || [])],
      confirmed: [...(newCards.confirmed || [])],
      completed: [...(newCards.completed || [])]
    }
  }
}, { immediate: true, deep: true })

const cardToConfirm = ref(null)
const showCreateModal = ref(false)
const showEditModal = ref(false)
const cardToEdit = ref(null)
const cardToDelete = ref(null)

const newCard = reactive({
  title: '',
  type: 'artist_performance',
  description: '',
  stage: 'backlog',
  due_date: ''
})

function canDropInStage(stage) {
  // Prevent dropping directly into confirmed
  return stage !== 'confirmed'
}

function onDragEnd(evt) {
  const card = evt.item.__draggable_component__.modelValue
  const newStage = evt.to.__draggable_component__.modelValue
  const newIndex = evt.newIndex

  // Find which stage this card is now in
  let targetStage = null
  for (const [stage, stageCards] of Object.entries(localCards.value)) {
    if (stageCards.includes(card)) {
      targetStage = stage
      break
    }
  }

  if (targetStage && targetStage !== 'confirmed') {
    router.patch(route('admin.kanban.move', card.id), {
      stage: targetStage,
      position: newIndex
    }, {
      preserveScroll: true
    })
  }
}

function confirmCard(card) {
  cardToConfirm.value = card
}

function editCard(card) {
  cardToEdit.value = { ...card }
  showEditModal.value = true
}

function updateCard() {
  router.put(route('admin.kanban.update', cardToEdit.value.id), {
    title: cardToEdit.value.title,
    description: cardToEdit.value.description,
    due_date: cardToEdit.value.dueDate
  }, {
    preserveState: false,
    onSuccess: () => {
      showEditModal.value = false
      cardToEdit.value = null
    }
  })
}

function deleteCard(card) {
  cardToDelete.value = card
}

function confirmDelete() {
  router.delete(route('admin.kanban.destroy', cardToDelete.value.id), {
    preserveState: false,
    onSuccess: () => {
      cardToDelete.value = null
    }
  })
}

function createCard() {
  router.post(route('admin.kanban.store'), newCard, {
    preserveState: false,
    onSuccess: () => {
      showCreateModal.value = false
      newCard.title = ''
      newCard.description = ''
      newCard.type = 'artist_performance'
      newCard.stage = 'backlog'
      newCard.due_date = ''
    }
  })
}

function formatType(type) {
  const types = {
    'artist_performance': 'Performance',
    'artist_appearance': 'Appearance',
    'content_filming': 'Content',
    'practice_day': 'Practice',
    'social_media_post': 'Social'
  }
  return types[type] || type
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-MY', { month: 'short', day: 'numeric' })
}
</script>
