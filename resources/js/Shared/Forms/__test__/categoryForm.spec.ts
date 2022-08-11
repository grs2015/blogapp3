import { mount } from "@vue/test-utils";
import CategoryForm from '@/Shared/Forms/CategoryForm.vue'
import { i18nVue } from "laravel-vue-i18n";
import { Quasar } from "quasar";
import { Inertia } from '@inertiajs/inertia'

const mockPost = vi.spyOn(Inertia, 'post').mockImplementation(() => console.log('Sending Post request'))
const mockPut = vi.spyOn(Inertia, 'put').mockImplementation(() => console.log('Sending Put request'))

let wrapper = null

interface Data {
    content?: string,
    icon?: string,
    id: number,
    color: string,
    'meta_title'?: string,
    'parent_id'?: number,
    slug?: string,
    title: string
}

const data: Data = {
    content: 'test category',
    icon: 'description',
    color: 'purple',
    id: 1,
    'meta_title': 'test category meta',
    slug: 'test-category',
    title: 'category title'
}

beforeEach(async() => {
    wrapper = await mount(CategoryForm,{ global: {
        plugins: [i18nVue, Quasar] },
        props: { data: { category: data } },
        shallow: false })
    mockPut.mockReset()
    mockPost.mockReset()
})

afterEach(async () => await wrapper.unmount())

describe('CategoryFrom.vue component', () => {
    it('checks the data from parent is defined on component if passed', async () => {
        expect(wrapper.props().data.category).toEqual(data)
    })

    it('checks the titles in absence/presence of input data', async () => {
        wrapper = await mount(CategoryForm,{ global: {
            plugins: [i18nVue, Quasar] },
            props: { data: { category: data } },
            shallow: false })

        expect(wrapper.text()).toContain('Edit Category')
        let submitBtn = wrapper.find('[data-test="submission-button"]')
        expect(submitBtn.text()).toContain('Update')

        wrapper = await mount(CategoryForm,{ global: {
            plugins: [i18nVue, Quasar] },
            props: { data: { category: null } },
            shallow: false })

        expect(wrapper.text()).toContain('Create Category')
        submitBtn = wrapper.find('[data-test="submission-button"]')
        expect(submitBtn.text()).toContain('Create')
    })

    // data-test="massdelete-button"

    it('checks that component renders all sub-components', () => {
        expect(wrapper.find('[data-test="title-input"]').exists()).toBe(true)
        expect(wrapper.find('[data-test="description-input"]').exists()).toBe(true)
        expect(wrapper.find('[data-test="meta-input"]').exists()).toBe(true)
        expect(wrapper.find('[data-test="expanded-list"]').exists()).toBe(true)
        expect(wrapper.find('[data-test="color-picker"]').exists()).toBe(true)
    })
})
