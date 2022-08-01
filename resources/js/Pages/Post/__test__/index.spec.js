import { shallowMount } from '@vue/test-utils'
import Index from '../Index.vue'

describe('First test', () => {
    test('Nothing to test', () => {
        const wrapper = shallowMount(Index, { props: { model: { posts: { data: [] } } } })
        expect(true).toBe(true)
    })
})
