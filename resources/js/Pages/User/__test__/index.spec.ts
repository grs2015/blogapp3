import { mount } from "@vue/test-utils";
import Index from '@/Pages/User/Index.vue'
import { Head } from "@inertiajs/inertia-vue3";
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import UserTable from '@/Shared/Tables/UserTable.vue'
import { UserStatus } from '@/Interfaces/PaginatedData'

let wrapper = null;

interface Data {
    avatar?: string,
    email: string,
    first_name: string
    id: number,
    intro?: string,
    last_login?: string,
    last_name?: string,
    middle_name?: string,
    mobile?: string,
    posts_count?: number,
    profile?: string,
    registered_at?: string,
    roles?: Array<string>
    status?: UserStatus
}

const data: Array<Data> = [{
    email: 'gbs@gbs.com',
    first_name: 'WooTang',
    id: 1,
    status: UserStatus.Pending
}]

beforeEach(async () => {
    wrapper = await mount(Index, { props: { model: { users: data } }, shallow: true } )
})

afterEach(async () => await wrapper.unmount())

describe('User/Index component', () => {
    it('checks the data from BE is defined on component', () => {
        expect(wrapper.props().model.users[0]).toEqual({
            email: 'gbs@gbs.com',
            first_name: 'WooTang',
            id: 1,
            status: UserStatus.Pending
        })
    })

    it("checks the existence & renders the Head with appropriate title data", () => {
        expect(wrapper.findComponent(Head).exists()).toBe(true);
        expect(wrapper.findComponent(Head).attributes("title")).toBe(
            "Blog users"
        );
    });

    it("checks the existence & passes to Breadcrumbs component the props", () => {
        const breadcrumbsComponent = wrapper.findComponent(Breadcrumbs);

        expect(breadcrumbsComponent.exists()).toBe(true);
        expect(breadcrumbsComponent.props().data).toEqual([
            { label: "Dashboard", icon: "home", route: "/admin" },
            { label: "Users", icon: "people_alt" },
        ]);
    });

    it("checks the existence & passes to PostTable component the props", () => {
        const userTableComponent = wrapper.findComponent(UserTable);

        expect(userTableComponent.exists()).toBe(true);
        expect(userTableComponent.props().paginatedData).toEqual(data);
    });
})
