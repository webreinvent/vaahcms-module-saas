<script setup>
import {onMounted, ref, watch} from "vue";
import { useRootStore } from '@/stores/root'
import { useServerV3Store } from '@/stores/store-serversv3'

import {useRoute} from 'vue-router';


const root = useRootStore();
const store = useServerV3Store();
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
                        data-testid="serversv3-view_item"
                        @click="store.toView(store.item)"
                        icon="pi pi-eye"/>

                <Button label="Save"
                        class="p-button-sm"
                        v-if="store.item && store.item.id"
                        data-testid="serversv3-save"
                        @click="store.itemAction('save')"
                        icon="pi pi-save"/>

                <Button label="Create & New"
                        v-else
                        @click="store.itemAction('create-and-new')"
                        class="p-button-sm"
                        data-testid="serversv3-create-and-new"
                        icon="pi pi-save"/>


                <!--form_menu-->
                <Button
                        type="button"
                        @click="toggleFormMenu"
                        class="p-button-sm"
                        data-testid="serversv3-form-menu"
                        icon="pi pi-angle-down"
                        aria-haspopup="true"/>

                <Menu ref="form_menu"
                      :model="store.form_menu_list"
                      :popup="true" />
                <!--/form_menu-->


                <Button class="p-button-primary p-button-sm"
                        icon="pi pi-times"
                        data-testid="serversv3-to-list"
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
                                data-testid="serversv3-item-restore"
                                @click="store.itemAction('restore')">
                        </Button>
                    </div>

                </div>

            </Message>


            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="serversv3-name"
                           data-testid="serversv3-name"
                           id="serversv3-name"
                           @update:modelValue="store.watchItem"
                           v-model="store.item.name" required/>
                <label for="serversv3-name">Enter the name</label>
            </FloatLabel>


            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="serversv3-slug"
                           data-testid="serversv3-slug"
                           id="serversv3-slug"
                           v-model="store.item.slug" required/>
                <label for="serversv3-slug">Enter the slug</label>
            </FloatLabel>


            <div class="flex items-center gap-2 my-3" >
                <ToggleSwitch v-bind:false-value="0"
                              v-bind:true-value="1"
                              size="small"
                              name="serversv3-active"
                              data-testid="serversv3-active"
                              inputId="serversv3-active"
                              v-model="store.item.is_active"/>

                <label for="serversv3-active">Remember Me</label>
            </div>


        </div>
    </Panel>


</template>
