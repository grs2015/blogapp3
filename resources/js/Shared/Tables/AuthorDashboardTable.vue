<script setup lang="ts">

import { DashboardPostData } from '@/Interfaces/DashboardData';
import { Status } from '@/Interfaces/PaginatedData';
import { computed } from 'vue'
import { trans } from 'laravel-vue-i18n';

import RecentPostsTable from '@/Shared/Tables/DashboardTables/Author/RecentPostsTable.vue'
import RatedPostsTable from '@/Shared/Tables/DashboardTables/Author/RatedPostsTable.vue'
import ViewedPostsTable from '@/Shared/Tables/DashboardTables/Author/ViewedPostsTable.vue'
import CommentedPostsTable from '@/Shared/Tables/DashboardTables/Author/CommentedPostsTable.vue'

interface Props {
    dashboardData: DashboardPostData
}

const props = defineProps<Props>()

const rowsRecentPosts = computed(() => props.dashboardData.recent_posts)
const rowsRatedPosts = computed(() => props.dashboardData.most_rated)
const rowsViewedPosts = computed(() => props.dashboardData.most_viewed)
const rowsCommentedPosts = computed(() => props.dashboardData.most_commented)

</script>

<template>
    <div class="q-mt-md">
        <q-card flat bordered>
            <q-card-section class="text-primary text-h6">
                {{ $t('My Post Statistics') }}
            </q-card-section>
            <q-card-section>
                <div class="row items-top q-col-gutter-md">
                    <div class="col-3">
                        <q-card bordered flat>
                            <q-card-section class="bg-amber-1">
                                <div class="text-primary">{{ $t('Overall posts:') }}</div>
                            </q-card-section>
                            <q-card-section>
                                <div class="text-h3 text-center text-primary">{{ props.dashboardData.posts_count }}</div>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div class="col-3">
                        <q-card bordered flat>
                            <q-card-section class="bg-green-1">
                                <div class="text-primary">{{ $t('Published posts:') }}</div>
                            </q-card-section>
                            <q-card-section>

                                <div class="text-h3 text-center text-primary">{{ props.dashboardData.published_posts_count }}</div>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div class="col-3">
                        <q-card bordered flat>
                            <q-card-section class="bg-red-1">
                                <div class="text-primary">{{ $t('Pending posts:') }}</div>
                            </q-card-section>
                            <q-card-section>
                                <div class="text-h3 text-center text-primary">{{ props.dashboardData.pending_posts_count }}</div>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div class="col-3">
                        <q-card bordered flat color="primary">
                            <q-card-section class="bg-grey-2">
                                <div class="text-primary">{{ $t('Draft posts:') }}</div>
                            </q-card-section>
                            <q-card-section>
                                <div class="text-h3 text-center text-primary">{{ props.dashboardData.draft_posts_count }}</div>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div class="col-6">
                        <q-list bordered class="rounded-borders">
                            <q-expansion-item
                                header-class="text-primary bg-grey-2"
                                expand-separator
                                icon="schedule"
                                :label="$t('Recently added posts with pending status')">
                                <RecentPostsTable :data="rowsRecentPosts" />
                            </q-expansion-item>
                        </q-list>
                    </div>
                    <div class="col-6">
                        <q-list bordered class="rounded-borders">
                            <q-expansion-item
                                header-class="text-primary bg-grey-2"
                                expand-separator
                                icon="thumb_up_off_alt"
                                :label="$t('Most rated posts')">
                                <RatedPostsTable :data="rowsRatedPosts" />
                            </q-expansion-item>
                        </q-list>
                    </div>
                    <div class="col-6">
                        <q-list bordered class="rounded-borders">
                            <q-expansion-item
                                header-class="text-primary bg-grey-2"
                                expand-separator
                                icon="visibility"
                                :label="$t('Most viewed posts')">
                                <ViewedPostsTable :data="rowsViewedPosts" />
                            </q-expansion-item>
                        </q-list>
                    </div>
                    <div class="col-6">
                        <q-list bordered class="rounded-borders">
                            <q-expansion-item
                            header-class="text-primary bg-grey-2"
                            expand-separator
                            icon="chat"
                            :label="$t('Most commented posts')" >
                            <CommentedPostsTable :data="rowsCommentedPosts" />
                        </q-expansion-item>
                        </q-list>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    </div>

</template>
