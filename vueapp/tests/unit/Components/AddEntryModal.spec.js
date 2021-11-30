import { shallowMount, mount, createLocalVue } from '@vue/test-utils'
import Buefy from 'buefy'
import AddEntryModal from '@/components/AddEntryModal'
import axios from 'axios'
//import errorMixin from '@/mixins/errorsMixin'
import flushPromises from "flush-promises";
const GlobalPlugins = {
  install(v) {
    v.prototype.$api = axios
  },
};

let wrapper
let localVue
let spyfun = jest.spyOn(AddEntryModal.methods, 'showError')
//jest.mock("axios");
  const mocks = {
    $api: axios
  };
  jest.mock("axios");
describe('AddEntryModal.vue', () => {
  beforeEach(async () => {
    localVue = createLocalVue()
    localVue.use(Buefy);
   // localVue.mixin(errorMixin)
    // localVue.use(GlobalPlugins)
    wrapper = await mount(AddEntryModal, {
      localVue, mocks
    })
  })
  afterEach(() => wrapper.destroy());

  it('renders a modal', () => {
    const wr = shallowMount(AddEntryModal, { localVue })
    expect(wr).toMatchSnapshot()
  })
  it('success from api', async () => {
    axios.get.mockResolvedValue({data: { result: 'success' } })
 
    const input = wrapper.find('input')
    input.element.value = 'testvalue'
    input.trigger('input')
    await wrapper.findAll('button').at(2).trigger('click')
   // await wrapper.vm.lookupAPI()
    await wrapper.vm.$nextTick()
    expect(wrapper.emitted('close')).toBeTruthy()
    expect(wrapper.emitted('runedition')).toBeTruthy()
    // axios.get.mockImplementation(() => Promise.resolve({data: {data: {result: 'success'}}}));
    //    expect(wrapper.emitted().runedition).toBeTruthy()


  })
  it('error from api', async () => {
    axios.get.mockResolvedValue({ data: { result: 'error', 'msg': 'error' } })
 
    const input = wrapper.find('input')
    input.element.value = 'testvalue'
    input.trigger('input')
    await wrapper.findAll('button').at(2).trigger('click')
    //await wrapper.vm.$nextTick()
    //await flushPromises();
    //console.log(spyfun.mock)
    expect(spyfun).toBeCalledWith('threadid', 'error')
  })
})