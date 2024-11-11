<script setup>
import {onMounted, ref, watch} from "vue";
import { useRootStore } from '@/stores/root'
import { useTenantV3Store } from '@/stores/store-tenantsv3'

import {useRoute} from 'vue-router';


const root = useRootStore();
const store = useTenantV3Store();
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
                        data-testid="tenantsv3-view_item"
                        @click="store.toView(store.item)"
                        icon="pi pi-eye"/>

                <Button label="Save"
                        class="p-button-sm"
                        v-if="store.item && store.item.id"
                        data-testid="tenantsv3-save"
                        @click="store.itemAction('save')"
                        icon="pi pi-save"/>

                <Button label="Create & New"
                        v-else
                        @click="store.itemAction('create-and-new')"
                        class="p-button-sm"
                        data-testid="tenantsv3-create-and-new"
                        icon="pi pi-save"/>


                <!--form_menu-->
                <Button
                        type="button"
                        @click="toggleFormMenu"
                        class="p-button-sm"
                        data-testid="tenantsv3-form-menu"
                        icon="pi pi-angle-down"
                        aria-haspopup="true"/>

                <Menu ref="form_menu"
                      :model="store.form_menu_list"
                      :popup="true" />
                <!--/form_menu-->


                <Button class="p-button-primary p-button-sm"
                        icon="pi pi-times"
                        data-testid="tenantsv3-to-list"
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
                                data-testid="tenantsv3-item-restore"
                                @click="store.itemAction('restore')">
                        </Button>
                    </div>

                </div>

            </Message>
            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <AutoComplete
                    v-model="store.selected_server"
                    field="vh_saas_server_id"
                    :suggestions="store.server_suggestions"
                    optionLabel="name"
                placeholder="Search & Select Server"
                @complete="store.searchServer"
                class="w-full"
                dusk="tenants-server"
                />
            </FloatLabel>


            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="tenantsv3-name"
                           data-testid="tenantsv3-name"
                           id="tenantsv3-name"
                           @update:modelValue="store.watchItem"
                           v-model="store.item.name" required/>
                <label for="tenantsv3-name">Enter the name</label>
            </FloatLabel>
            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="tenantsv3-slug"
                           data-testid="tenantsv3-slug"
                           id="tenantsv3-slug"
                           v-model="store.item.slug" required/>
                <label for="tenantsv3-slug">Enter the slug</label>
            </FloatLabel>
            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="tenantsv3-path"
                           data-testid="tenantsv3-path"
                           id="tenantsv3-path"
                           v-model="store.item.path" required/>
                <label for="tenantsv3-path">Enter the path</label>
            </FloatLabel>
            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="tenantsv3-domain"
                           data-testid="tenantsv3-domain"
                           id="tenantsv3-domain"
                           v-model="store.item.domain" required/>
                <label for="tenantsv3-domain">Enter the domain</label>
            </FloatLabel>
            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="tenantsv3-sub_domain"
                           data-testid="tenantsv3-sub_domain"
                           id="tenantsv3-sub_domain"
                           v-model="store.item.sub_domain" required/>
                <label for="tenantsv3-sub_domain">Enter the sub-domain</label>
            </FloatLabel>
            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="tenantsv3-database_name"
                           data-testid="tenantsv3-database_name"
                           id="tenantsv3-database_name"
                           v-model="store.item.database_name" required/>
                <label for="tenantsv3-database_name">Enter the database name</label>
            </FloatLabel>
            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="tenantsv3-database_username"
                           data-testid="tenantsv3-database_username"
                           id="tenantsv3-database_username"
                           v-model="store.item.database_username" required/>
                <label for="tenantsv3-database_username">Enter the database username</label>
            </FloatLabel>
            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="tenantsv3-database_password"
                           data-testid="tenantsv3-database_password"
                           id="tenantsv3-database_password"
                           v-model="store.item.database_password" required/>
                <label for="tenantsv3-database_password">Enter the database password</label>
            </FloatLabel>
            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="tenantsv3-database_charset"
                           data-testid="tenantsv3-database_charset"
                           id="tenantsv3-database_charset"
                           v-model="store.item.database_charset" required/>
                <label for="tenantsv3-database_charset">Enter the database charset</label>
            </FloatLabel>
            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="tenantsv3-database_collation"
                           data-testid="tenantsv3-database_collation"
                           id="tenantsv3-database_collation"
                           v-model="store.item.database_collation" required/>
                <label for="tenantsv3-database_collation">Enter the database collation</label>
            </FloatLabel>
            <!-- Database SSL Mode Selection -->
            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <Select
                    v-model="store.item.database_sslmode"
                    :options="store.assets.database_sslmodes"
                    optionLabel="name"
                    optionValue="name"
                    placeholder="Database SSL Mode"
                    class="w-full"
                />
            </FloatLabel>

            <!-- Conditional SSL Fields -->
            <div v-if="store.item.database_sslmode && store.item.database_sslmode !== 'disable'" class="mt-4">
                <FloatLabel class="my-3" :variant="store.float_label_variants">
                    <InputText
                        v-model="store.item.meta.ssl_key_path"
                        placeholder="SSL Key Path"
                        class="w-full"
                    />
                </FloatLabel>

                <FloatLabel class="my-3" :variant="store.float_label_variants">
                    <InputText
                        v-model="store.item.meta.cert_path"
                        placeholder="CERT Path"
                        class="w-full"
                    />
                </FloatLabel>

                <FloatLabel class="my-3" :variant="store.float_label_variants">
                    <InputText
                        v-model="store.item.meta.ssl_ca_path"
                        placeholder="SSL CA Path"
                        class="w-full"
                    />
                </FloatLabel>
            </div>





            <FloatLabel class="my-3" :variant="store.float_label_variants">
    <textarea class="w-full"
              name="tenantsv3-notes"
              data-testid="tenantsv3-notes"
              id="tenantsv3-notes"
              maxlength="200"
              v-model="store.item.notes"
              required></textarea>
                <label for="tenantsv3-notes">Enter notes</label>
            </FloatLabel>
            <div class="flex items-center gap-2 my-3" >
                <ToggleSwitch v-bind:false-value="0"
                              v-bind:true-value="1"
                              size="small"
                              name="tenantsv3-active"
                              data-testid="tenantsv3-active"
                              inputId="tenantsv3-active"
                              v-model="store.item.is_active"/>

                <label for="tenantsv3-active">Is Active</label>
            </div>


        </div>
    </Panel>


</template>
