import { mount } from "@vue/test-utils";
import TagForm from '@/Shared/Forms/TagForm.vue'
import { i18nVue } from "laravel-vue-i18n";
import { Quasar } from "quasar";
import { Inertia } from '@inertiajs/inertia'

const mockPost = vi.spyOn(Inertia, 'post').mockImplementation(() => console.log('Sending Post request'))
const mockPut = vi.spyOn(Inertia, 'put').mockImplementation(() => console.log('Sending Put request'))

let wrapper = null

interface Data {
    id: number,
    title: string,
    content?: string,
    meta_title?: string,
    slug?: string
}

const data: Data = {
    id: 1,
    title: 'Tag title',
    content: 'Tag content',
    meta_title: 'Meta data',
    slug: 'tag-title'
}

beforeEach(async() => {
    wrapper = await mount(TagForm,{ global: {
        plugins: [i18nVue, Quasar] },
        props: { data: { tag: data } },
        shallow: false })
    mockPut.mockReset()
    mockPost.mockReset()
})

afterEach(async () => await wrapper.unmount())

describe('TagFrom.vue component', () => {
    it('checks the data from parent is defined on component if passed', async () => {
        expect(wrapper.props().data.tag).toEqual(data)
    })

    it('checks the titles in absence/presence of input data', async () => {
        wrapper = await mount(TagForm,{ global: {
            plugins: [i18nVue, Quasar] },
            props: { data: { tag: data } },
            shallow: false })

        expect(wrapper.text()).toContain('Edit Tag')
        let submitBtn = wrapper.find('[data-test="submission-button"]')
        expect(submitBtn.text()).toContain('Update')

        wrapper = await mount(TagForm,{ global: {
            plugins: [i18nVue, Quasar] },
            props: { data: { tag: null } },
            shallow: false })

        expect(wrapper.text()).toContain('Create Tag')
        submitBtn = wrapper.find('[data-test="submission-button"]')
        expect(submitBtn.text()).toContain('Create')
    })

    // data-test="massdelete-button"

    it('checks that component renders all sub-components', () => {
        expect(wrapper.find('[data-test="title-input"]').exists()).toBe(true)
        expect(wrapper.find('[data-test="description-input"]').exists()).toBe(true)
        expect(wrapper.find('[data-test="meta-input"]').exists()).toBe(true)
    })
})







