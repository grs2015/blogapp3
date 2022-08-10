import { mount } from "@vue/test-utils";
import CategoryTable from "@/Shared/Tables/CategoryTable.vue"
import { i18nVue } from "laravel-vue-i18n";
import { Quasar } from "quasar";
import { Inertia } from '@inertiajs/inertia'

const mockGet = vi.spyOn(Inertia, 'get').mockImplementation(() => console.log('Sending Get request'))
const mockDelete = vi.spyOn(Inertia, 'delete').mockImplementation(() => console.log('Sending Delete request'))

let wrapper = null

const paginatedData = {
    current_page: 1,
    data: [
        { id: 1, title: 'first_title', color: 'purple', parent_id: 1, icon: 'description', slug: 'slug', content: 'content', meta_title: 'meta' },
        { id: 2, title: 'first_title', color: 'purple', parent_id: 1, icon: 'description', slug: 'slug', content: 'content', meta_title: 'meta' },
    ],
    first_page_url: "first_page_url",
    from: 1,
    last_page: 4,
    last_page_url: "last_page_url",
    links: [ { active: true, label: 'active', url: 'link' }, { active: false, label: 'inactive', url: 'link' } ],
    next_page_url: "next_page_url",
    path: "path",
    per_page: 5,
    prev_page_url: "prev_page_url",
    to: 4,
    total: 20
}
const sortingData = { column: 'title', descending: 'true' }

beforeEach(async() => {
    wrapper = await mount(CategoryTable,{ global: {
        plugins: [i18nVue, Quasar] },
        props: { paginatedData, sortingData },
        shallow: false })
    mockGet.mockReset()
    mockDelete.mockReset()
})

afterEach(async () => await wrapper.unmount());

describe('CategoryTable component', () => {
    it('checks the data from parent component is defined on current component', () => {
        expect(wrapper.element).toMatchSnapshot();

        expect(wrapper.props().paginatedData).toContain({
            current_page: 1,
            first_page_url: 'first_page_url',
            last_page_url: 'last_page_url',
            prev_page_url: 'prev_page_url',
        })
        expect(wrapper.props().paginatedData.data[0]).toContain({
            title: 'first_title',
            slug: 'slug',
            color: 'purple'
        })
    })

    it('checks the number of row in table (1 - Head, 2 - Body)', () => {
        expect(wrapper.findAll('tr')).toHaveLength(3)
    })

    it('checks the edit button events correcly hit the Inertia back-end', async () => {
        const editBtn = wrapper.find('[data-test="edit-button"]')

        await editBtn.trigger('click')
        expect(Inertia.get).toHaveBeenCalledTimes(1)
        expect(Inertia.get).toHaveBeenCalledWith("/admin/categories/slug/edit")
    })

    it('checks the delete button events correcly hit the Inertia back-end', async () => {
        const deleteBtn = wrapper.find('[data-test="delete-button"]')

        await deleteBtn.trigger('click')
        expect(Inertia.delete).toHaveBeenCalledTimes(1)
        expect(Inertia.delete).toHaveBeenCalledWith("/admin/categories/slug", expect.anything())
    })

    it('checks the refresh button events correcly hit the Inertia back-end', async () => {
        const refreshBtn = wrapper.find('[data-test="refresh-button"]')

        await refreshBtn.trigger('click')
        expect(Inertia.get).toHaveBeenCalledTimes(1)
        expect(Inertia.get).toHaveBeenCalledWith("/admin/categories")
    })

    it('checks the add button events correcly hit the Inertia back-end', async () => {
        const addBtn = wrapper.find('[data-test="add-button"]')

        await addBtn.trigger('click')
        expect(Inertia.get).toHaveBeenCalledTimes(1)
        expect(Inertia.get).toHaveBeenCalledWith("/admin/categories/create")
    })
})
