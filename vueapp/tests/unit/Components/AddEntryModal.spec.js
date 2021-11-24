import { shallowMount, mount, createLocalVue } from '@vue/test-utils'
import Buefy from 'buefy'
import AddEntryModal from '@/components/AddEntryModal'
import axios from 'axios'
const GlobalPlugins = {
    install(v) {
      v.prototype.$api = axios
    },
  };

const localVue = createLocalVue();
localVue.use(Buefy);
localVue.use(GlobalPlugins)

//jest.mock("axios");

describe('AddEntryModal.vue', () =>
{
    const mocks = {
        $api: {
          get: Promise.resolve({data: {data: {result: 'success'}}}),
         
        },
      };
    it('renders a modal', () =>
    {
        const wrapper = shallowMount(AddEntryModal, { localVue })
        expect(wrapper).toMatchSnapshot()
    })
    it('success from api', async () =>
    {
        jest.spyOn(axios, 'get').mockResolvedValue({data: {data: {result: 'success'}}});
        const wrapper = mount(AddEntryModal, { localVue  })
        const input = wrapper.find('input')
        input.element.value = 'testvalue'
        input.trigger('input')
        const button = wrapper.find('button.is-primary')
         button.trigger('click')
       await wrapper.vm.$nextTick(()=>
       {
           console.log(wrapper.vm)
        expect(wrapper.emitted().close).toHaveBeenCalled()
        done()
       }) 
      // axios.get.mockImplementation(() => Promise.resolve({data: {data: {result: 'success'}}}));
    //    expect(wrapper.emitted().runedition).toBeTruthy()
       


    })
})