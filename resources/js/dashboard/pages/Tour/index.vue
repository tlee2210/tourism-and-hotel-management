<template>
  <a-card title="Tour List management" style="width: 100%" class="shadow">
    <form @submit.prevent="searchTour" enctype="multipart/form-data">
      <div class="row mb-4">
        <div class="col-12 col-sm-4">
          <label>
            <span>search name</span>
          </label>
          <a-input placeholder="input name tour" allow-clear v-model:value="searchName">
            <template #prefix>
              <font-awesome-icon :icon="['fas', 'file-signature']" />
            </template>
          </a-input>
        </div>

        <div class="col-12 col-sm-2">
          <label>
            <span>place</span>
          </label>
          <a-select
            show-search
            placeholder="place seclect"
            style="width: 100%"
            :options="Places"
            :filter-option="filterplace"
            v-model:value="searchPlace_id"
            allow-clear
          >
            <template #suffixIcon>
              <font-awesome-icon :icon="['fas', 'location-dot']" /> </template
          ></a-select>
        </div>
        <div class="col-12 col-sm-2">
          <label>
            <span>category</span>
          </label>
          <a-select
            show-search
            placeholder="category seclect"
            style="width: 100%"
            :options="category"
            :filter-option="filtercategory"
            v-model:value="searchcategory_id"
            allow-clear
          >
            <template #suffixIcon>
              <font-awesome-icon :icon="['fas', 'location-dot']" /> </template
          ></a-select>
        </div>
        <div class="col-12 col-sm-2">
          <label>
            <span>status</span>
          </label>
          <a-select
            show-search
            placeholder="seclect status"
            style="width: 100%"
            :options="statusOptions"
            :filter-option="filterOption"
            v-model:value="searchStatus"
            allow-clear
          >
            <template #suffixIcon>
              <font-awesome-icon :icon="['fas', 'bookmark']" /> </template
          ></a-select>
        </div>
        <div class="col-12 col-sm-2 btn-search">
          <a-button
            type="primary"
            class="ml-2"
            htmlType="submit"
            style="padding: 0px 30px"
          >
            <span>search</span>
          </a-button>
        </div>
      </div>
    </form>

    <div class="row">
      <div class="col-12">
        <a-table :dataSource="tours" :columns="columns" :scroll="{ x: 1500 }">
          <template #expandedRowRender="{ record }">
            <Descriptions title="Tour Info" layout="vertical" bordered>
              <Descriptions label="Category">
                <p>{{ record.categoryName }}</p>
              </Descriptions>
              <Descriptions label="duration">
                <p>{{ record.duration }} DAY</p>
              </Descriptions>
              <Descriptions label="introduce">
                <div v-html="record.introduce"></div>
              </Descriptions>
              <Descriptions label="schedule">
                <div v-html="record.schedule"></div>
              </Descriptions>
            </Descriptions>
          </template>

          <template #bodyCell="{ column, index, record }">
            <template v-if="column.key === 'index'">
              <span>{{ index + 1 }}</span>
            </template>

            <template v-if="column.key === 'images'">
              <a-image
                :preview="{ visible: false }"
                :width="110"
                :src="record.paths[0]"
                @click="visibleStates[record.id] = true"
              />
              <div style="display: none">
                <a-image-preview-group
                  :preview="{
                    visible: visibleStates[record.id] || false,
                    onVisibleChange: (vis) => (visibleStates[record.id] = vis),
                  }"
                >
                  <a-image
                    v-for="(path, index) in record.paths"
                    :key="index"
                    :src="path"
                    class="img-fluid me-1 mb-1"
                    style="width: 100px; height: 70px"
                  />
                </a-image-preview-group>
              </div>
            </template>
            <template v-if="column.key === 'prominent'">
              <div class="form-check form-switch">
                <input
                  class="form-check-input fs-5"
                  type="checkbox"
                  role="switch"
                  @click="prominentRecord(record.id)"
                  :id="record.prominent"
                  :checked="record.prominent === 1"
                />
              </div>
            </template>

            <template v-if="column.key === 'status'">
              <a-tag color="success" v-if="record.status === 'active'">{{
                record.status
              }}</a-tag>
              <a-tag color="error" v-if="record.status === 'inactive'">{{
                record.status
              }}</a-tag>
            </template>

            <template v-if="column.key === 'action'">
              <router-link :to="{ name: 'tour-time', params: { slug: record.slug } }">
                <a-button type="primary" class="me-2">
                  <font-awesome-icon :icon="['fas', 'list']" class="me-1" />
                  <span>time</span>
                </a-button>
              </router-link>

              <router-link :to="{ name: 'tour-edit', params: { slug: record.slug } }">
                <a-button type="primary" class="me-2 mt-2">
                  <font-awesome-icon :icon="['fas', 'pen-to-square']" />
                </a-button>
              </router-link>

              <a-button
                type="primary"
                danger
                class="mt-2"
                @click="deleteRecord(record.id)"
                ><font-awesome-icon :icon="['fas', 'trash']"
              /></a-button>
              <!-- @click="deleteRecord(record.id)" -->
            </template>
          </template>
        </a-table>
      </div>
    </div>
  </a-card>
</template>

<script>
import axios from "axios";
import { useMenu } from "../../../store/menu";
import { ref, defineComponent, inject, reactive, toRefs } from "vue";
import { message, Descriptions } from "ant-design-vue";
import { useRouter, useRoute } from "vue-router";

export default defineComponent({
  setup() {
    const store = useMenu();
    store.onselectedkey(["tour_list"]);

    const $loading = inject("$loading");

    const search = reactive({
      searchName: "",
      searchPlace_id: null,
      searchcategory_id: null,
      searchStatus: null,
    });

    const visible = ref(false);
    const visibleStates = ref({});
    const Places = ref([]);
    const category = ref([]);
    const tours = ref([]);
    const router = useRouter();

    const columns = [
      {
        title: "Tour Code",
        dataIndex: "tour_Code",
        key: "tour_Code",
        width: 100,
      },
      {
        title: "Images",
        dataIndex: "paths",
        key: "images",
        width: 200,
      },
      {
        title: "Name Tour",
        dataIndex: "title",
        key: "title",
      },
      {
        title: "place",
        dataIndex: "placeName",
        key: "placeName",
        width: "200px",
      },
      {
        title: "prominent",
        key: "prominent",
      },
      {
        title: "status",
        dataIndex: "status",
        key: "status",
        width: "100px",
      },
      {
        title: "action",
        key: "action",
        fixed: "right",
        width: "250px",
      },
    ];

    const statusOptions = [
      {
        label: "Active",
        value: "active",
      },
      {
        label: "On Hold",
        value: "On Hold",
      },
    ];

    const getTour = () => {
      const loader = $loading.show({});
      axios
        .get(`http://127.0.0.1:8000/api/dashboard/tour`)
        .then(function (response) {
          tours.value = response.data.data.tours;
          category.value = response.data.data.category;
          Places.value = response.data.data.places;
          loader.hide();
        })
        .catch(function (error) {
          console.error(error);
          loader.hide();
        });
    };

    const deleteRecord = (recordId) => {
      const loader = $loading.show({});
      axios
        .post(`http://127.0.0.1:8000/api/dashboard/tour/delete/${recordId}`)
        .then(function (response) {
          // console.log(response);
          loader.hide();
          if (response.data.message) {
            message.success(response.data.message);
            getTour();
          }
        })
        .catch(function (error) {
          console.log(error);
          if (error.response.status === 400) {
            message.error(error.response.data.message);
          } else {
            message.error(error.response.data.message);
          }
          loader.hide();
        });
    };

    const filterplace = (input, Places) => {
      return Places.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
    };

    const filtercategory = (input, category) => {
      return category.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
    };

    const filterOption = (input, statusOptions) => {
      return statusOptions.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
    };

    getTour();

    const searchTour = () => {
      const loader = $loading.show({});
      const formData = new FormData();
      if (search.searchName) {
        formData.append("searchName", search.searchName);
      }
      if (search.searchPlace_id) {
        formData.append("searchPlace_id", search.searchPlace_id);
      }
      if (search.searchcategory_id) {
        formData.append("searchcategory_id", search.searchcategory_id);
      }
      if (search.searchStatus) {
        formData.append("searchStatus", search.searchStatus);
      }
      axios
        .post("http://127.0.0.1:8000/api/dashboard/tour/search", formData)
        .then(function (response) {
          // console.log(response);
          tours.value = response.data.data.tours;
          loader.hide();
        })
        .catch(function (error) {
          console.error(error);
          loader.hide();
        });
    };

    const prominentRecord = (recordId) => {
      const loader = $loading.show({});
      axios
        .get(`http://127.0.0.1:8000/api/dashboard/tour/prominent/${recordId}`)
        .then(function (response) {
          // console.log(response);
          loader.hide();
          message.success(response.data.message);
        })
        .catch(function (error) {
          // console.log(error);
          loader.hide();
          message.error(error.response.data.message);
        });
    };

    return {
      tours,
      Places,
      category,
      ...toRefs(search),
      prominentRecord,
      filterplace,
      filtercategory,
      filterOption,
      statusOptions,
      searchTour,
      columns,
      getTour,
      deleteRecord,
      visible,
      visibleStates,
    };
  },
  components: {
    Descriptions,
  },
});
</script>
<style>
.btn-search {
  margin: 22px 0px 0px 0px;
}
</style>
