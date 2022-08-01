import { mount } from '@vue/test-utils'
import Index from '@/Pages/Post/Index.vue'
import { Head } from '@inertiajs/inertia-vue3'

describe('First test', () => {
    test('Nothing to test', () => {
        const wrapper = mount(Index, { props: { model: { posts: { data: [] } } }, shallow: true })
        expect(wrapper.findComponent(Head).exists()).toBe(true)
        expect(wrapper.findComponent(Head).attributes('title')).toBe('Blog posts')
        expect(true).toBe(true)

    })
})
