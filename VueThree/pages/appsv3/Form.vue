<script setup>
import {onMounted, ref, watch} from "vue";
import { useRootStore } from '@/stores/root'
import { useAppV3Store } from '@/stores/store-appsv3'

import {useRoute} from 'vue-router';


const root = useRootStore();
const store = useAppV3Store();
const route = useRoute();

onMounted(async () => {
    /**
     * Fetch the record from the database
     */
    if((!store.item || Object.keys(store.item).length < 1)
            && route.params && route.params.id)
    {
        await store.getItem(route.params.id);
    }

    await store.getFormMenu();
});

//--------form_menu
const form_menu = ref();
const toggleFormMenu = (event) => {
    form_menu.value.toggle(event);
};
//--------/form_menu

</script>
<template>


    <Panel :pt="root.panel_pt">

        <template class="p-1" #header>


            <div class="flex flex-row">
                <div class="p-panel-title">
                        <span v-if="store.item && store.item.id">
                            Update
                        </span>
                    <span v-else>
                            Create
                        </span>
                </div>

            </div>


        </template>

        <template #icons>


            <div class="p-inputgroup">

                <Button class="p-button-sm"
                        v-tooltip.left="'View'"
                        v-if="store.item && store.item.id"
                        data-testid="appsv3-view_item"
                        @click="store.toView(store.item)"
                        icon="pi pi-eye"/>

                <Button label="Save"
                        class="p-button-sm"
                        v-if="store.item && store.item.id"
                        data-testid="appsv3-save"
                        @click="store.itemAction('save')"
                        icon="pi pi-save"/>

                <Button label="Create & New"
                        v-else
                        @click="store.itemAction('create-and-new')"
                        class="p-button-sm"
                        data-testid="appsv3-create-and-new"
                        icon="pi pi-save"/>


                <!--form_menu-->
                <Button
                        type="button"
                        @click="toggleFormMenu"
                        class="p-button-sm"
                        data-testid="appsv3-form-menu"
                        icon="pi pi-angle-down"
                        aria-haspopup="true"/>

                <Menu ref="form_menu"
                      :model="store.form_menu_list"
                      :popup="true" />
                <!--/form_menu-->


                <Button class="p-button-primary p-button-sm"
                        icon="pi pi-times"
                        data-testid="appsv3-to-list"
                        @click="store.toList()">
                </Button>
            </div>



        </template>

        <div v-if="store.item" class="mt-2">

            <Message severity="error"
                     class="p-container-message mb-3"
                     :closable="false"
                     icon="pi pi-trash"
                     v-if="store.item.deleted_at">

                <div class="flex align-items-center justify-content-between">

                    <div class="">
                        Deleted {{store.item.deleted_at}}
                    </div>

                    <div class="ml-3">
                        <Button label="Restore"
                                class="p-button-sm"
                                data-testid="appsv3-item-restore"
                                @click="store.itemAction('restore')">
                        </Button>
                    </div>

                </div>

            </Message>


            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <Select name="app-type"
                        :options="store.assets.app_type"
                        option-label="name"
                        option-value="name"
                        v-model="store.item.app_type"
                class="w-full"
                showClear />
                <label for="app_types">Select App Type</label>
            </FloatLabel>

            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <Select
                    v-if="store.item.app_type === 'Module'"
                    name="module-name"
                    :options="store.assets.modules"
                    option-label="name"
                    option-value="name"
                    v-model="store.item.name"
                    class="w-full"
                    showClear
                    @change="store.setNewItemValues"
                />
                <label v-if="store.item.app_type === 'Module'" for="select_module">Select Module</label>
            </FloatLabel>




            <FloatLabel v-if="store.item.app_type !== 'Module'" class="my-3" :variant="store.float_label_variants">
                <InputText
                    class="w-full"
                    name="appsv3-name"
                    data-testid="appsv3-name"
                    id="appsv3-name"
                    v-model="store.item.name"
                    required
                />
                <label for="appsv3-name">Name</label>
            </FloatLabel>



            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="appsv3-slug"
                           data-testid="appsv3-slug"
                           id="appsv3-slug"
                           v-model="store.item.slug" required/>
                <label for="appsv3-slug">Slug</label>
            </FloatLabel>

            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="appsv3-relative_path"
                           data-testid="appsv3-relative_path"
                           id="appsv3-relative_path"
                           v-model="store.item.relative_path" required/>
                <label for="appsv3-relative-path">Relative Path</label>
            </FloatLabel>

            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="appsv3-migration_path"
                           data-testid="appsv3-migration_path"
                           id="appsv3-migration_path"
                           v-model="store.item.migration_path" required/>
                <label for="appsv3-migration-path">Migration Path</label>
            </FloatLabel>

            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="appsv3-seed_class"
                           data-testid="appsv3-seed_class"
                           id="appsv3-seed_class"
                           v-model="store.item.seed_class" required/>
                <label for="appsv3-seed-class">Seed Class</label>
            </FloatLabel>

            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="appsv3-sample_data_class"
                           data-testid="appsv3-sample_data_class"
                           id="appsv3-sample_data_class"
                           v-model="store.item.sample_data_class" required/>
                <label for="appsv3-sample-data-class">Sample Data Class</label>
            </FloatLabel>

            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <Textarea v-model="store.item.excerpt" rows="5" cols="64"
                            maxlength="200" label="Excerpt"></Textarea>
                <label for="appsv3-Excerpt">Excerpt</label>
            </FloatLabel>

            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <Textarea v-model="store.item.notes" rows="5" cols="64"
                          maxlength="200" label="notes"></Textarea>
                <label for="appsv3-notes">Notes</label>
            </FloatLabel>

            <div class="flex items-center gap-2 my-3" >
                <ToggleSwitch v-bind:false-value="0"
                              v-bind:true-value="1"
                              size="small"
                              name="appsv3-active"
                              data-testid="appsv3-active"
                              inputId="appsv3-active"
                              v-model="store.item.is_active"/>

                <label for="appsv3-active">Is Active</label>


            </div>





        </div>
    </Panel>


</template>
