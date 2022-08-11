import { mount } from "@vue/test-utils";
import Form from '@/Pages/Category/Form.vue'
import CategoryForm from '@/Shared/Forms/CategoryForm.vue'
import { Head } from "@inertiajs/inertia-vue3";
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'

let wrapper = null;

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

afterEach(async () => await wrapper.unmount())

describe('Category/Form component', () => {
    it("makes a snapshot", async () => {
        wrapper = await mount(Form, { props: { model: { category: data } }, shallow: true } )

        expect(wrapper.element).toMatchSnapshot();
    });

    it('checks the data from BE is defined on component if passed', async () => {

        wrapper = await mount(Form, { props: { model: { category: data } }, shallow: true } )

        expect(wrapper.props().model.category).toEqual({
            content: 'test category',
            icon: 'description',
            color: 'purple',
            id: 1,
            'meta_title': 'test category meta',
            slug: 'test-category',
            title: 'category title'
        })
    })

    it("checks the existence & renders the Head with appropriate title data when props passed", async () => {

        wrapper = await mount(Form, { props: { model: { category: data } }, shallow: true } )

        expect(wrapper.findComponent(Head).exists()).toBe(true);
        expect(wrapper.findComponent(Head).attributes("title")).toBe(
            "Blog Category - Edit"
        );
    });

    it("checks the existence & renders the Head with appropriate title data when no props passed", async () => {

        wrapper = await mount(Form, { props: { model: { category: null } }, shallow: true } )

        expect(wrapper.findComponent(Head).exists()).toBe(true);
        expect(wrapper.findComponent(Head).attributes("title")).toBe(
            "Blog Category - Create"
        );
    });

    it("checks the existence & passes to Breadcrumbs component appropriate data when props passed", async () => {

        wrapper = await mount(Form, { props: { model: { category: data } }, shallow: true } )

        const breadcrumbsComponent = wrapper.findComponent(Breadcrumbs);

        expect(breadcrumbsComponent.exists()).toBe(true);
        expect(breadcrumbsComponent.props().data).toEqual([
            { label: "Dashboard", icon: "home", route: "/admin" },
            { label: "Categories", icon: "category", route: "/admin/categories" },
            { label: "Edit", icon: "edit" }
        ]);
    });

    it("checks the existence & passes to Breadcrumbs component appropriate data when no props passed", async () => {

        wrapper = await mount(Form, { props: { model: { category: null } }, shallow: true } )

        const breadcrumbsComponent = wrapper.findComponent(Breadcrumbs);

        expect(breadcrumbsComponent.exists()).toBe(true);
        expect(breadcrumbsComponent.props().data).toEqual([
            { label: "Dashboard", icon: "home", route: "/admin" },
            { label: "Categories", icon: "category", route: "/admin/categories" },
            { label: "Create", icon: "edit" }
        ]);
    });

    it("checks the existence & passes to CategoryForm component the props", async () => {

        wrapper = await mount(Form, { props: { model: { category: data } }, shallow: true } )

        const categoryFormComponent = wrapper.findComponent(CategoryForm);

        expect(categoryFormComponent.exists()).toBe(true);
        expect(categoryFormComponent.props().data.category).toEqual(data)
    });
})
