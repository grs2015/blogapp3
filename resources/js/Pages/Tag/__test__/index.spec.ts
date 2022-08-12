import { mount } from "@vue/test-utils";
import Index from '@/Pages/Tag/Index.vue'
import { Head } from "@inertiajs/inertia-vue3";
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import TagTable from '@/Shared/Tables/TagTable.vue'

let wrapper = null;

interface Data {
    content?: string,
    id: number,
    meta_title?: string,
    slug: string,
    title: string
}

const data: Array<Data> = [{
    content: 'test tag',
    id: 1,
    meta_title: 'test tag meta',
    slug: 'test-tag',
    title: 'tag title'
}]

beforeEach(async () => {
    wrapper = await mount(Index, { props: { model: { tags: data } }, shallow: true } )
})

afterEach(async () => await wrapper.unmount())

describe('Tag/Index component', () => {
    it('checks the data from BE is defined on component', () => {
        expect(wrapper.props().model.tags[0]).toEqual({
            content: 'test tag',
            id: 1,
            meta_title: 'test tag meta',
            slug: 'test-tag',
            title: 'tag title'
        })
    })

    it("checks the existence & renders the Head with appropriate title data", () => {
        expect(wrapper.findComponent(Head).exists()).toBe(true);
        expect(wrapper.findComponent(Head).attributes("title")).toBe(
            "Post tags"
        );
    });

    it("checks the existence & passes to Breadcrumbs component the props", () => {
        const breadcrumbsComponent = wrapper.findComponent(Breadcrumbs);

        expect(breadcrumbsComponent.exists()).toBe(true);
        expect(breadcrumbsComponent.props().data).toEqual([
            { label: "Dashboard", icon: "home", route: "/admin" },
            { label: "Tags", icon: "local_offer" },
        ]);
    });

    it("checks the existence & passes to PostTable component the props", () => {
        const tagTableComponent = wrapper.findComponent(TagTable);

        expect(tagTableComponent.exists()).toBe(true);
        expect(tagTableComponent.props().paginatedData).toEqual(data);
    });
})
