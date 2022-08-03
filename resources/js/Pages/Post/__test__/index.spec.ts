import { mount } from "@vue/test-utils";
import Index from "@/Pages/Post/Index.vue";
import { Head } from "@inertiajs/inertia-vue3";
import Breadcrumbs from "@/Shared/Breadcrumbs.vue";
import PostTable from "@/Shared/Tables/PostTable.vue";
import { i18nVue } from "laravel-vue-i18n";
import { Quasar } from "quasar";
import Layout from "@/Shared/Layout.vue";
import { ZiggyVue } from "/vendor/tightenco/ziggy/dist/vue";
import { Ziggy } from '@/ziggy';

let wrapper = null;

type Data = {
    current_page: number;
    last_page: number;
    per_page: number;
};

const data: Data = {
    current_page: 1,
    last_page: 4,
    per_page: 15,
};

beforeEach(async () => {
    wrapper = await mount(Index, {
        props: { model: { posts: data } },
        shallow: true,
    });
});
afterEach(async () => await wrapper.unmount());

describe("Post/Index component", () => {
    it("makes a snapshot", () => {
        expect(wrapper.element).toMatchSnapshot();
    });

    it("checks the data from BE is defined on component", () => {
        expect(wrapper.props().model.posts).toEqual({
            current_page: 1,
            last_page: 4,
            per_page: 15,
        });
    });

    it("checks the existence & renders the Head with appropriate title data", () => {
        expect(wrapper.findComponent(Head).exists()).toBe(true);
        expect(wrapper.findComponent(Head).attributes("title")).toBe(
            "Blog posts"
        );
    });

    it("checks the existence & passes to Breadcrumbs component the props", () => {
        const breadcrumbsComponent = wrapper.findComponent(Breadcrumbs);

        expect(breadcrumbsComponent.exists()).toBe(true);
        expect(breadcrumbsComponent.props().data).toEqual([
            { label: "Dashboard", icon: "home", route: "/admin" },
            { label: "Posts", icon: "widgets" },
        ]);
    });

    it("checks the existence & passes to PostTable component the props", () => {
        const postTableComponent = wrapper.findComponent(PostTable);

        expect(postTableComponent.exists()).toBe(true);
        expect(postTableComponent.props().paginatedData).toEqual(data);
    });

    // it("checks i18n", () => {
    //     const wrapper = mount(Breadcrumbs, {
    //         global: {
    //             plugins: [i18nVue, Quasar],
    //         },
    //         props: {
    //             data: [
    //                 {
    //                     label: "Dashboard",
    //                     icon: "home",
    //                     route: "/admin",
    //                 },
    //                 {
    //                     label: "Posts",
    //                     icon: "widgets",
    //                 },
    //             ],
    //         },
    //         shallow: false,
    //     });

    //     expect(wrapper.html()).toContain("Dashboard");
    //     // console.log(wrapper.html());
    // });

    // it('checks layout',async () => {
    //     const layout = mount(Layout, { shallow: false, global: {
    //         plugins: [i18nVue, Quasar, [ZiggyVue, Ziggy],]
    //     }  })
    //     const buttonDE = layout.find('[data-test="DE"]')
    //     await buttonDE.trigger('click')
    //     expect(layout.text()).toContain('Armaturenbrett')

    // })
});
