<script setup lang="ts">

import { DashboardData, RecentPost, UserData} from '@/Interfaces/DashboardData';
import { Status } from '@/Interfaces/PaginatedData';
import { computed } from 'vue'
import { trans } from 'laravel-vue-i18n';

import RecentPostsTable from '@/Shared/Tables/DashboardTables/RecentPostsTable.vue'
import RatedPostsTable from '@/Shared/Tables/DashboardTables/RatedPostsTable.vue'
import ViewedPostsTable from '@/Shared/Tables/DashboardTables/ViewedPostsTable.vue'
import CommentedPostsTable from '@/Shared/Tables/DashboardTables/CommentedPostsTable.vue'

import RecentUsersTable from '@/Shared/Tables/DashboardTables/RecentUsersTable.vue'
import ActiveUsersTable from '@/Shared/Tables/DashboardTables/ActiveUsersTable.vue'


interface Props {
    dashboardData: DashboardData
}

const props = defineProps<Props>()

const rowsRecentPosts = computed(() => props.dashboardData.recent_posts)
const rowsRatedPosts = computed(() => props.dashboardData.most_rated)
const rowsViewedPosts = computed(() => props.dashboardData.most_viewed)
const rowsCommentedPosts = computed(() => props.dashboardData.most_commented)

const rowsRecentUsers = computed(() => props.dashboardData.recent_users)
const rowsActiveUsers = computed(() => props.dashboardData.active_authors)

</script>

<template>
    <div class="q-mt-md">
        <q-card flat bordered>
            <q-card-section class="text-primary text-h6">
                {{ $t('Post Statistics') }}
            </q-card-section>
            <q-card-section>
                <div class="row items-top q-col-gutter-md">
                    <div class="col-3">
                        <q-card bordered flat>
                            <q-card-section class="bg-amber-1">
                                <div class="text-primary">Overall posts:</div>
                            </q-card-section>
                            <q-card-section>
                                <div class="text-h3 text-center text-primary">{{ props.dashboardData.posts_count }}</div>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div class="col-3">
                        <q-card bordered flat>
                            <q-card-section class="bg-green-1">
                                <div class="text-primary">Published posts:</div>
                            </q-card-section>
                            <q-card-section>

                                <div class="text-h3 text-center text-primary">{{ props.dashboardData.published_posts_count }}</div>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div class="col-3">
                        <q-card bordered flat>
                            <q-card-section class="bg-red-1">
                                <div class="text-primary">Pending posts:</div>
                            </q-card-section>
                            <q-card-section>
                                <div class="text-h3 text-center text-primary">{{ props.dashboardData.pending_posts_count }}</div>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div class="col-3">
                        <q-card bordered flat color="primary">
                            <q-card-section class="bg-grey-2">
                                <div class="text-primary">Draft posts:</div>
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
                                label="Recently added posts with pending status">
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
                                label="Most rated posts">
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
                                label="Most viewed posts">
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
                            label="Most commented posts" >
                            <CommentedPostsTable :data="rowsCommentedPosts" />
                        </q-expansion-item>
                        </q-list>
                    </div>
                </div>
            </q-card-section>
            <q-separator inset />
            <q-card-section class="text-primary text-h6">
                {{ $t('User Statistics') }}
            </q-card-section>
            <q-card-section>
                <div class="row items-top q-col-gutter-md">
                    <div class="col-3">
                        <q-card bordered flat>
                            <q-card-section class="bg-blue-1">
                                <div class="text-primary">Author users:</div>
                            </q-card-section>
                            <q-card-section>
                                <div class="text-h3 text-center text-primary">{{ props.dashboardData.count_author_users }}</div>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div class="col-3">
                        <q-card bordered flat>
                            <q-card-section class="bg-blue-1">
                                <div class="text-primary">Member users:</div>
                            </q-card-section>
                            <q-card-section>
                                <div class="text-h3 text-center text-primary">{{ props.dashboardData.count_member_users }}</div>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div class="col-3">
                        <q-card bordered flat>
                            <q-card-section class="bg-green-1">
                                <div class="text-primary">Enabled users:</div>
                            </q-card-section>
                            <q-card-section>
                                <div class="text-h3 text-center text-primary">{{ props.dashboardData.enabled_users_count }}</div>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div class="col-3">
                        <q-card bordered flat>
                            <q-card-section class="bg-red-1">
                                <div class="text-primary">Pending users:</div>
                            </q-card-section>
                            <q-card-section>
                                <div class="text-h3 text-center text-primary">{{ props.dashboardData.pending_users_count }}</div>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div class="col-6">
                        <q-list bordered class="rounded-borders">
                            <q-expansion-item
                                header-class="text-primary bg-grey-2"
                                expand-separator
                                icon="person_add"
                                label="Recently added users with pending status">
                                <RecentUsersTable :data="rowsRecentUsers" />
                            </q-expansion-item>
                        </q-list>
                    </div>
                    <div class="col-6">
                        <q-list bordered class="rounded-borders">
                            <q-expansion-item
                                header-class="text-primary bg-grey-2"
                                expand-separator
                                icon="emoji_events"
                                label="Most active authors">
                                <ActiveUsersTable :data="rowsActiveUsers" />
                            </q-expansion-item>
                        </q-list>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    </div>
</template>
