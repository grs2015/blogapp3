import { mount } from "@vue/test-utils";
import PostTable from "@/Shared/Tables/PostTable.vue"
import { Paginated } from "@/Interfaces/PaginatedData";
import { i18nVue } from "laravel-vue-i18n";
import { Quasar } from "quasar";
import { Favorite, Status } from "@/Interfaces/PaginatedData";
import { Inertia } from '@inertiajs/inertia'

vi.spyOn(Inertia, 'get').mockImplementation(() => console.log('Sending Get request'))
vi.spyOn(Inertia, 'delete').mockImplementation(() => console.log('Sending Delete request'))

let wrapper = null

const paginatedData = {
    current_page: 1,
    data: [
        { id: 1, title: 'first_title', favorite: Favorite.Usual, parent_id: 1, views: '1', slug: 'slug', status: Status.Draft, time_to_read: 2, user: { email: 'email', first_name: 'name' } },
        { id: 2, title: 'first_title', favorite: Favorite.Usual, parent_id: 1, views: '1', slug: 'slug', status: Status.Draft, time_to_read: 2, user: { email: 'email', first_name: 'name' } },
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

    wrapper = await mount(PostTable,{ global: {
        plugins: [i18nVue, Quasar] },
        props: { paginatedData, sortingData },
        shallow: false })
    // console.log(wrapper.html())
})
afterEach(async () => await wrapper.unmount());

describe('PostTable component', () => {
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
            status: 'draft'
        })
    })

    it('checks the number of row in table (1 - Head, 2 - Body)', () => {
        expect(wrapper.findAll('tr')).toHaveLength(3)
    })

    it('checks the button events correcly hit the Inertia back-end', async () => {
        const editBtn = wrapper.find('[data-test="edit-button"]')
        const deleteBtn = wrapper.find('[data-test="delete-button"]')
        const refreshBtn = wrapper.find('[data-test="refresh-button"]')
        const addBtn = wrapper.find('[data-test="add-button"]')

        await refreshBtn.trigger('click')
        expect(Inertia.get).toHaveBeenCalledTimes(1)
        expect(Inertia.get).toHaveBeenCalledWith("/admin/posts")

        await editBtn.trigger('click')
        expect(Inertia.get).toHaveBeenCalledTimes(2)
        expect(Inertia.get).toHaveBeenCalledWith("/admin/posts/slug/edit")

        await addBtn.trigger('click')
        expect(Inertia.get).toHaveBeenCalledTimes(3)
        expect(Inertia.get).toHaveBeenCalledWith("/admin/posts/create")

        await deleteBtn.trigger('click')
        expect(Inertia.delete).toHaveBeenCalledTimes(1)
        expect(Inertia.delete).toHaveBeenCalledWith("/admin/posts/slug")
    })
})
