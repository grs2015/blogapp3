<script setup lang="ts">
import { categoryData } from '@/Interfaces/PaginatedData';
import { trans } from 'laravel-vue-i18n';
import { useQuasar } from 'quasar'
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/inertia-vue3'
import icons from '@/iconSet.js'


interface Props {
    data: {
        category?: categoryData | null
    }
}

const props = defineProps<Props>()

const $q = useQuasar()
const modeCreate = ref(true)
const confirm = ref(false)
const catTitleRef = ref(null) // For validation resetting of title field in the form

let form = useForm<categoryData>({
    id: null,
    title: "",
    content: "",
    meta_title: "",
    icon: "category",
    color: "#00d600"
})

if (props.data.category) {
    modeCreate.value = false

    form = useForm<categoryData>({
        id: props.data.category.id,
        title: props.data.category.title,
        meta_title: props.data.category.meta_title,
        content: props.data.category.content,
        parent_id: props.data.category.parent_id,
        slug: props.data.category.slug,
        icon: props.data.category.icon,
        color: props.data.category.color
    })
}

const actionCategory = () => {
    if (modeCreate.value) {
        form.post('/admin/categories', {
        onSuccess: () => onStoreSuccess(),
        onError: () => onStoreFail()
        })
        return
    }

    form.put(`/admin/categories/${form.slug}`, {
        onSuccess: () => onUpdateSuccess(),
        onError: () => onUpdateFail(),
    })
}

const onStoreSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans('The category have been stored successfully')
    })
}

const onStoreFail = () => {
    $q.notify({
        type: 'negative',
        message: trans('There were errors while storing category: ') +  form.errors.title
    })
}

const onUpdateSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans('The category have been updated successfully')
    })
}

const onUpdateFail = () => {
    $q.notify({
        type: 'negative',
        message: trans('There were errors while updating: ') + trans('validation.required')
    })
}


const resetForm = () => {
    form.reset()
    catTitleRef.value.resetValidation()
}

</script>

<template>
    <div class="q-mt-md">
        <q-form @submit="actionCategory">
            <q-card flat bordered>
                <q-card-section class="text-primary text-h6">
                    {{ modeCreate ? $t('Create Category') : $t('Edit Category') }}
                </q-card-section>
                <q-card-section>
                    <div class="column q-col-gutter-md">
                        <div class="row q-col-gutter-md">
                            <div class="col column">
                                <q-card flat bordered>
                                    <q-card-section>
                                        <div class="col q-mb-md">
                                            <div class="form_header text-subtitle2 text-primary text-weight-thin">
                                                {{ $t('Category Title') }}</div>
                                            <q-input v-model="form.title" dense data-test="title-input"
                                                :rules="[val => !!val || 'Field is required']" ref="catTitleRef">
                                                <template v-slot:prepend>
                                                    <q-icon name="edit" color="orange" />
                                                </template>
                                            </q-input>
                                        </div>
                                        <div class="col q-mb-md">
                                            <div class="form_header text-subtitle2 text-primary text-weight-thin">
                                                {{ $t('Category Description') }}</div>
                                            <q-input v-model="form.content" dense data-test="description-input">
                                                <template v-slot:prepend>
                                                    <q-icon name="edit" color="orange" />
                                                </template>
                                            </q-input>
                                        </div>
                                        <div class="col q-mb-md">
                                            <div class="form_header text-subtitle2 text-primary text-weight-thin">
                                                {{ $t('Category Meta Description (SEO)') }}</div>
                                            <q-input v-model="form.meta_title" dense data-test="meta-input">
                                                <template v-slot:prepend>
                                                    <q-icon name="edit" color="orange" />
                                                </template>
                                            </q-input>
                                        </div>
                                        <div class="col">
                                            <q-list bordered class="rounded-borders">
                                                <q-expansion-item
                                                expand-separator
                                                icon="find_in_page"
                                                :label="$t('Pick the category icon')"
                                                header-class="text-primary"
                                                data-test="expanded-list"
                                                >
                                                <q-card>
                                                    <q-card-section>
                                                        <div class="row q-col-gutter-x-md items-stretch">
                                                            <div class="col-9">
                                                                <div class="text-subtitle2 text-primary text-weight-thin">
                                                                    {{ $t('Available icons') }}
                                                                </div>
                                                                <q-separator spaced />
                                                                <div class="row q-col-gutter-sm">
                                                                    <div v-for="(icon, idx) in icons" :key="idx">
                                                                        <div class="row rounded-borders flex-center bg-grey-5">
                                                                            <q-btn size="lg" padding="xs" flat :icon="icon" class="text-white" @click="form.icon = icon" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-3">
                                                                <div class="text-subtitle2 text-primary text-weight-thin">
                                                                    {{ $t('Picked icon') }}
                                                                </div>
                                                                <q-separator spaced />
                                                                <div class="row flex-center" style="height: 75%">
                                                                    <div class="row rounded-borders flex-center color-box-pick" :style="{background: form.color}">
                                                                        <q-icon :name="form.icon" color="white" size="xl"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </q-card-section>
                                                </q-card>
                                                </q-expansion-item>
                                            </q-list>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </div>
                            <div class="col-auto">
                                <q-card flat bordered>
                                    <q-card-section class="column">
                                        <div class="form_header text-subtitle2 text-primary text-weight-thin">
                                            {{ $t('Pick category color') }}</div>
                                        <q-separator spaced />
                                        <div class="row items-center justify-start q-col-gutter-x-md">
                                            <div class="text-subtitle2 text-primary text-weight-thin">{{ $t('Example of picked color:') }}</div>
                                            <div >
                                                <div class="row rounded-borders flex-center color-box" :style="{background: form.color}">
                                                    <q-icon name="sentiment_very_satisfied" color="white" size="md"/>
                                                </div>
                                            </div>
                                        </div>
                                        <q-separator spaced />
                                        <q-color v-model="form.color" flat no-header no-footer default-view="palette" data-test="color-picker"
                                            class="cat_color_picker"/>
                                    </q-card-section>
                                </q-card>

                            </div>
                        </div>
                    </div>
                </q-card-section>
                <q-card-section>
                    <div class="row justify-between items-center">
                        <div>
                            <q-btn outline color="negative" @click="confirm = true" :disable="form.processing">{{
                            $t('Reset') }}</q-btn>
                        </div>
                        <div class="row q-col-gutter-md">
                            <div>
                                <Link as="div" href="/admin/categories">
                                <q-btn outline color="secondary" :disable="form.processing">{{ $t('Cancel') }}</q-btn>
                                </Link>
                            </div>
                            <div>
                                <q-btn type="submit" outline color="primary" :loading="form.processing" data-test="submission-button">{{ modeCreate ?
                                $t('Create') : $t('Update') }}
                                    <template v-slot:loading>
                                        <q-spinner-hourglass />
                                    </template>
                                </q-btn>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </q-form>
    </div>
    <q-dialog v-model="confirm" persistent>
        <q-card>
            <q-card-section class="row items-center bg-red-1">
                <q-avatar icon="front_hand" color="negative" text-color="white" />
                <span class="q-ml-sm" data-test="reset-text">{{ $t('You can lose all of your data put in the form') }}</span>
            </q-card-section>

            <q-card-actions align="right">
                <q-btn flat :label="$t('Cancel')" color="primary" v-close-popup />
                <q-btn flat :label="$t('Reset')" color="negative" v-close-popup @click="resetForm"/>
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>

<style lang="sass" scoped>
.cat_color_picker
    // max-width: 250px
    min-width: 300px

.color-box
    width: 35px
    height: 35px

.color-box-pick
    width: 50px
    height: 50px

</style>
