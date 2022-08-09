import { mount } from "@vue/test-utils";
import Index from '@/Pages/Category/Index.vue'
import { Head } from "@inertiajs/inertia-vue3";
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import CategoryTable from '@/Shared/Tables/CategoryTable.vue'

let wrapper = null;

interface Data {
    content?: string,
    icon?: string,
    id: number,
    'meta_title'?: string,
    'parent_id'?: number,
    slug?: string,
    title: string
}

const data: Array<Data> = [{
    content: 'test category',
    icon: 'description',
    id: 1,
    'meta_title': 'test category meta',
    slug: 'test-category',
    title: 'category title'
}]

beforeEach(async () => {
    wrapper = await mount(Index, { props: { model: { categories: data } }, shallow: true } )
})

afterEach(async () => await wrapper.unmount())

describe('Category/Index component', () => {
    it('checks the data from BE is defined on component', () => {
        expect(wrapper.props().model.categories[0]).toEqual({
            content: 'test category',
            icon: 'description',
            id: 1,
            'meta_title': 'test category meta',
            slug: 'test-category',
            title: 'category title'
        })
    })

    it("checks the existence & renders the Head with appropriate title data", () => {
        expect(wrapper.findComponent(Head).exists()).toBe(true);
        expect(wrapper.findComponent(Head).attributes("title")).toBe(
            "Post categories"
        );
    });

    it("checks the existence & passes to Breadcrumbs component the props", () => {
        const breadcrumbsComponent = wrapper.findComponent(Breadcrumbs);

        expect(breadcrumbsComponent.exists()).toBe(true);
        expect(breadcrumbsComponent.props().data).toEqual([
            { label: "Dashboard", icon: "home", route: "/admin" },
            { label: "Categories", icon: "category" },
        ]);
    });

    it("checks the existence & passes to PostTable component the props", () => {
        const postTableComponent = wrapper.findComponent(CategoryTable);

        expect(postTableComponent.exists()).toBe(true);
        expect(postTableComponent.props().paginatedData).toEqual(data);
    });
})
