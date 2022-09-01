<script setup lang="ts">
import { CommentData } from '@/Interfaces/PaginatedData';
import { ref, reactive, nextTick, computed } from 'vue'
import { formatDistanceToNow } from 'date-fns'


interface Props {
    data: Array<CommentData>,
    loading: boolean
}

interface Events {
    (e: 'status', { id: number, status: string }): void
    (e: 'delete', { id: number }): void
}

const props = defineProps<Props>()
const emit = defineEmits<Events>()


const commentStatuses = reactive({})
const loading = computed(() => props.loading)
const currentComment = ref(null)

props.data.forEach((item, idx) => {
    commentStatuses[`${item.id}`] = ref(`${item.status}`)
})

const statusChanged = (id: number) => {
    currentComment.value = id
    emit('status', { id: id, status: commentStatuses[id] })
}

const deleteComment = (id: number) => {
    currentComment.value = id
    emit('delete', { id: id })
}

</script>

<template>
    <q-list bordered class="rounded-borders">
        <q-expansion-item
            expand-separator
            icon="message"
            :label="$t('Post comments')"
            header-class="text-primary bg-grey-2"
            data-test="expanded-list"
        >
            <q-virtual-scroll
                style="max-height: 300px;"
                :items="props.data"
                separator
                v-slot="{ item, index }"
            >
                <q-item :key="index" :disable="loading">
                        <q-item-section avatar>
                            <template v-if="loading && item.id === currentComment">
                                <q-spinner-gears
                                    color="primary"
                                    size="2em"
                                />
                            </template>
                            <template v-else>
                                <q-icon name="chat_bubble" :class="item.status === 'pending' ? 'text-teal-1' : item.status === 'published' ? 'text-teal' : 'text-teal-3'" />
                            </template>
                        </q-item-section>
                        <q-item-section>
                            <q-item-label class="text-primary">{{ item.title }} <span class="text-caption text-grey text-italic"> Commented by: {{ item.author }} - {{ item.published_at }}</span></q-item-label>
                            <q-item-label caption lines="2">{{ item.content }}</q-item-label>
                        </q-item-section>
                        <q-item-section side>
                            <div class="row items-center">
                                <q-item-label caption class="q-mr-md text-primary">
                                    {{ formatDistanceToNow(new Date(item.published_at), {addSuffix: true}) }}
                                </q-item-label>
                                <template v-if="item.status === 'pending'">
                                    <q-toggle
                                    v-model="commentStatuses[`${item.id}`]"
                                    @update:model-value="statusChanged(item.id)"
                                    false-value="pending"
                                    true-value="published"
                                    checked-icon="visibility"
                                    unchecked-icon="hourglass_empty">
                                        <q-tooltip anchor="bottom middle" self="center middle" :delay="500">
                                            {{ $t('Pending > Published') }}
                                        </q-tooltip>
                                    </q-toggle>
                                </template>
                                <template v-else>
                                    <q-toggle
                                    v-model="commentStatuses[`${item.id}`]"
                                    @update:model-value="statusChanged(item.id)"
                                    false-value="unpublished"
                                    true-value="published"
                                    checked-icon="visibility"
                                    unchecked-icon="visibility_off">
                                        <q-tooltip anchor="bottom middle" self="center middle" :delay="500">
                                            {{ $t('Unpublished > Published') }}
                                        </q-tooltip>
                                    </q-toggle>
                                </template>
                                <q-btn @click="deleteComment(item.id)" class="gt-xs text-red" size="12px" flat dense round icon="delete" />
                            </div>
                        </q-item-section>
                </q-item>
            </q-virtual-scroll>
            <!-- <q-list>
                <template v-for="(comment, index) in props.data" :key="index">
                    <q-item>
                        <q-item-section avatar>
                            <q-icon name="chat_bubble" color="teal-1" />
                        </q-item-section>
                        <q-item-section>
                            <q-item-label class="text-primary">{{ comment.title }}</q-item-label>
                            <q-item-label caption lines="2">{{ comment.content }}</q-item-label>
                        </q-item-section>
                        <q-item-section side>
                            <div class="row items-center">
                                <q-item-label caption class="q-mr-md text-primary">
                                    {{ formatDistanceToNow(new Date(comment.published_at), {addSuffix: true}) }}
                                </q-item-label>
                                <q-toggle
                                    v-model="commentStatuses[`${comment.id}`]"
                                    false-value="pending"
                                    true-value="published"
                                    checked-icon="visibility"
                                    unchecked-icon="hourglass_empty"

                                />
                                <q-btn class="gt-xs text-red" size="12px" flat dense round icon="delete" />
                            </div>

                        </q-item-section>
                    </q-item>
                    <q-separator v-if="index !== props.data.length - 1" inset="item" />
                </template>
            </q-list> -->
        </q-expansion-item>
    </q-list>
</template>
