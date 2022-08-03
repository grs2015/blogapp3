import { mount } from "@vue/test-utils";
import { i18nVue } from "laravel-vue-i18n";
import { Quasar } from "quasar";
import Breadcrumbs from "@/Shared/Breadcrumbs.vue";

const breadcrumbsData = [
    {
        label: "Dashboard",
        icon: "home",
        route: "/admin",
    },
    {
        label: "Posts",
        icon: "widgets",
    },
]

describe("Breadcrumbs Component", () => {
    it('checks rendered text/links at component', () => {
        const wrapper = mount(Breadcrumbs, {
            global: {
                plugins: [i18nVue, Quasar],
            },
            props: { data: breadcrumbsData },
            shallow: false,
        });

        expect(wrapper.element).toMatchSnapshot();

        expect(wrapper.html()).toContain("Dashboard");
        expect(wrapper.html()).toContain("Posts");

        expect(wrapper.find('a').attributes('href')).toBe('/admin')
        expect(wrapper.find('a').text()).toContain('Dashboard')
    })
})
