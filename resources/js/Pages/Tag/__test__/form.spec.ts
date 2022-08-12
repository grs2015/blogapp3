import { mount } from "@vue/test-utils";
import Form from '@/Pages/Tag/Form.vue'
import TagForm from '@/Shared/Forms/TagForm.vue'
import { Head } from "@inertiajs/inertia-vue3";
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'

let wrapper = null;

interface Data {
    content?: string,
    id: number,
    meta_title?: string,
    slug: string,
    title: string
}

const data: Data = {
    content: 'test tag',
    id: 1,
    meta_title: 'test tag meta',
    slug: 'test-tag',
    title: 'tag title'
}

afterEach(async () => await wrapper.unmount())

describe('Tag/Form component', () => {
    it("makes a snapshot", async () => {
        wrapper = await mount(Form, { props: { model: { tag: data } }, shallow: true } )

        expect(wrapper.element).toMatchSnapshot();
    });

    it('checks the data from BE is defined on component if passed', async () => {

        wrapper = await mount(Form, { props: { model: { tag: data } }, shallow: true } )

        expect(wrapper.props().model.tag).toEqual({
            content: 'test tag',
            id: 1,
            meta_title: 'test tag meta',
            slug: 'test-tag',
            title: 'tag title'
        })
    })

    it("checks the existence & renders the Head with appropriate title data when props passed", async () => {

        wrapper = await mount(Form, { props: { model: { tag: data } }, shallow: true } )

        expect(wrapper.findComponent(Head).exists()).toBe(true);
        expect(wrapper.findComponent(Head).attributes("title")).toBe(
            "Blog Tag - Edit"
        );
    });

    it("checks the existence & renders the Head with appropriate title data when no props passed", async () => {

        wrapper = await mount(Form, { props: { model: { tag: null } }, shallow: true } )

        expect(wrapper.findComponent(Head).exists()).toBe(true);
        expect(wrapper.findComponent(Head).attributes("title")).toBe(
            "Blog Tag - Create"
        );
    });

    it("checks the existence & passes to Breadcrumbs component appropriate data when props passed", async () => {

        wrapper = await mount(Form, { props: { model: { tag: data } }, shallow: true } )

        const breadcrumbsComponent = wrapper.findComponent(Breadcrumbs);

        expect(breadcrumbsComponent.exists()).toBe(true);
        expect(breadcrumbsComponent.props().data).toEqual([
            { label: "Dashboard", icon: "home", route: "/admin" },
            { label: "Tags", icon: "local_offer", route: "/admin/tags" },
            { label: "Edit", icon: "edit" }
        ]);
    });

    it("checks the existence & passes to Breadcrumbs component appropriate data when no props passed", async () => {

        wrapper = await mount(Form, { props: { model: { tag: null } }, shallow: true } )

        const breadcrumbsComponent = wrapper.findComponent(Breadcrumbs);

        expect(breadcrumbsComponent.exists()).toBe(true);
        expect(breadcrumbsComponent.props().data).toEqual([
            { label: "Dashboard", icon: "home", route: "/admin" },
            { label: "Tags", icon: "local_offer", route: "/admin/tags" },
            { label: "Create", icon: "edit" }
        ]);
    });

    it("checks the existence & passes to TagForm component the props", async () => {

        wrapper = await mount(Form, { props: { model: { tag: data } }, shallow: true } )

        const tagFormComponent = wrapper.findComponent(TagForm);

        expect(tagFormComponent.exists()).toBe(true);
        expect(tagFormComponent.props().data.tag).toEqual(data)
    });
})
