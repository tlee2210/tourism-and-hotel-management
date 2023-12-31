<template>
  <a-card title="Category management" style="width: 100%" class="shadow">
    <div class="row">
      <div class="col-12 d-flex justify-content-end me-2">
        <a-button type="primary" @click="showModal">
          <font-awesome-icon :icon="['fas', 'plus']" />
        </a-button>
      </div>
    </div>
    <div class="row mb-4">
      <div class="col-12 col-sm-6">
        <form @submit.prevent="searchCategory()" enctype="multipart/form-data">
          <div class="row">
            <a-input-search
              v-model:value="searchName"
              placeholder="input search"
              enter-button
              allow-clear
            />
          </div>
        </form>
      </div>
    </div>
    <Modal v-model:open="open">
      <template #title> {{ modalTitle }} </template>
      <template #footer>
        <form
          @submit.prevent="checkform ? createCategory() : updateCategory(ID)"
          enctype="multipart/form-data"
        >
          <div class="row">
            <div class="col-12 col-sm-9 mb-4 justify-content-center align-items-center">
              <div class="row mb-4">
                <div class="col-12 col-sm-2 text-start text-sm-center">
                  <label>
                    <span class="text-danger me-1">*</span>
                    <span>name:</span>
                  </label>
                </div>
                <div class="col-12 col-sm-9">
                  <a-input
                    placeholder="enter the widget name"
                    allow-clear
                    v-model:value="name"
                  />
                  <div class="w-100"></div>
                  <small v-if="errors.name" class="text-danger">{{
                    errors.name[0]
                  }}</small>
                </div>
              </div>
            </div>
          </div>
          <a-button key="back" @click="handleCancel">Cancel</a-button>
          <a-button key="submit" type="primary" htmlType="submit">Save</a-button>
        </form>
      </template>
    </Modal>

    <div class="row mt-5">
      <div class="col-12">
        <a-table :dataSource="Categorydata" :columns="columns" :scroll="{ x: 576 }">
          <template #bodyCell="{ column, index, record }">
            <template v-if="column.key === 'index'">
              <span>{{ index + 1 }}</span>
            </template>

            <template v-if="column.key === 'action'">
              <a-button type="primary" @click="showEdit(record.id)" class="me-2">
                <font-awesome-icon :icon="['fas', 'pen-to-square']" />
              </a-button>
              <a-button type="primary" danger @click="deleteRecord(record.id)"
                ><font-awesome-icon :icon="['fas', 'trash']"
              /></a-button>
            </template>
          </template>
        </a-table>
      </div>
    </div>
  </a-card>
</template>

<script>
import { defineComponent, ref, reactive, toRefs, inject } from "vue";
import { Modal, message } from "ant-design-vue";
import { useMenu } from "../../../store/menu";

export default defineComponent({
  setup() {
    const $loading = inject("$loading");
    const store = useMenu();
    store.onselectedkey(["category"]);
    const errors = ref({});

    const open = ref(false);
    const checkform = ref(true);
    const modalTitle = ref("Create Category");

    const showModal = () => {
      open.value = true;
      modalTitle.value = "Create Category";
      checkform.value = true;
      errors.value = {};
    };

    const handleCancel = () => {
      open.value = false;
    };
    const Categorydata = ref([]);

    const CategoryCreate = reactive({
      name: "",
      ID: "",
    });

    const columns = [
      {
        title: "#",
        key: "index",
        width: 70,
      },
      {
        title: "name",
        dataIndex: "name",
        key: "name",
        width: 1000,
      },
      {
        title: "action",
        key: "action",
        fixed: "right",
        width: 150,
      },
    ];

    const showEdit = (record) => {
      showModal();
      modalTitle.value = "Edit Category";
      getEdit(record);
      checkform.value = false;
      errors.value = {};
    };

    const createCategory = () => {
      const loader = $loading.show({});
      const formData = new FormData();
      formData.append("name", CategoryCreate.name);
      axios
        .post("http://127.0.0.1:8000/api/dashboard/category/create", formData)
        .then(function (response) {
          console.log(response);
          loader.hide();
          if (response.data.message) {
            getCategory();
            message.success(response.data.message);
            CategoryCreate.name = "";
            open.value = false;
          }
        })
        .catch(function (error) {
          // console.log(error);
          loader.hide();
          errors.value = error.response.data.errors;
        });
    };

    const getCategory = () => {
      const loader = $loading.show({});
      axios
        .get(`http://127.0.0.1:8000/api/dashboard/category`)
        .then(function (response) {
          // console.log(response);
          Categorydata.value = response.data.data.categoty;
          loader.hide();
        })
        .catch(function (error) {
          console.log(error);
          loader.hide();
        });
    };

    const getEdit = (record) => {
      const loader = $loading.show({});
      axios
        .get(`http://127.0.0.1:8000/api/dashboard/category/${record}/edit`)
        .then(function (response) {
          // console.log(response);
          CategoryCreate.ID = response.data.data.category.id;
          CategoryCreate.name = response.data.data.category.name;
          loader.hide();
        })
        .catch(function (error) {
          console.log(error);
          loader.hide();
        });
    };

    const updateCategory = (record) => {
      const loader = $loading.show({});
      const formData = new FormData();
      formData.append("name", CategoryCreate.name);
      axios
        .post(`http://127.0.0.1:8000/api/dashboard/category/${record}/edit`, formData)

        .then(function (response) {
          // console.log(response);
          loader.hide();
          if (response.data.message) {
            getCategory();
            message.success(response.data.message);
            CategoryCreate.name = "";
            open.value = false;
          }
        })
        .catch(function (error) {
          // console.log(error);
          loader.hide();
          errors.value = error.response.data.errors;
        });
    };

    const deleteRecord = (recordId) => {
      const loader = $loading.show({});
      axios
        .post(`http://127.0.0.1:8000/api/dashboard/category/delete/${recordId}`)
        .then(function (response) {
          // console.log(response);
          loader.hide();
          if (response.data.message) {
            message.success(response.data.message);
            getCategory();
          }
        })
        .catch(function (error) {
          console.log(error);
          message.error(error.response.data.message);
          loader.hide();
        });
    };
    const search = reactive({
      searchName: "",
    });

    const searchCategory = () => {
      const loader = $loading.show({});
      const formData = new FormData();

      formData.append("searchName", search.searchName ? search.searchName : "");
      axios
        .post(`http://127.0.0.1:8000/api/dashboard/category/search`, formData)
        .then(function (response) {
          console.log(response);
          if (response) {
            Categorydata.value = response.data.data.categoty;
          }
          loader.hide();
        })
        .catch(function (error) {
          console.log(error);
          message.error(error.response.data.message);
          loader.hide();
        });
    };

    getCategory();
    return {
      Categorydata,
      open,
      columns,
      errors,
      modalTitle,
      showModal,
      ...toRefs(CategoryCreate),
      handleCancel,
      createCategory,
      deleteRecord,
      getEdit,
      showEdit,
      updateCategory,
      checkform,
      searchCategory,
      ...toRefs(search),
    };
  },
  components: {
    Modal,
  },
});
</script>

<style></style>
