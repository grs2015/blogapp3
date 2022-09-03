<script setup lang="ts">

import { CommentData } from '@/Interfaces/PaginatedData';

interface Props {
    data?: Array<CommentData>
}

const props = defineProps<Props>()

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
                <q-item :key="index">
                        <q-item-section avatar>
                            <q-icon name="chat_bubble_outline" class="text-grey" />
                        </q-item-section>
                        <q-item-section>
                            <q-item-label class="text-primary">{{ item.title }} <span class="text-caption text-grey text-italic"> {{ $t('Commented by:') }} {{ item.author }} - {{ item.published_at }}</span></q-item-label>
                            <q-item-label caption lines="2">{{ item.content }}</q-item-label>
                        </q-item-section>

                </q-item>
            </q-virtual-scroll>

        </q-expansion-item>
    </q-list>

</template>
