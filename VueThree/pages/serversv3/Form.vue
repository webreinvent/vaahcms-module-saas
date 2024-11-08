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

        <!-- Header Section -->
        <template #header>
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

        <!-- Icons and Actions Section -->
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

                <!-- Form Menu -->
                <Button type="button"
                        @click="toggleFormMenu"
                        class="p-button-sm"
                        data-testid="serversv3-form-menu"
                        icon="pi pi-angle-down"
                        aria-haspopup="true"/>

                <Menu ref="form_menu"
                      :model="store.form_menu_list"
                      :popup="true" />
                <!-- End of Form Menu -->

                <Button class="p-button-primary p-button-sm"
                        icon="pi pi-times"
                        data-testid="serversv3-to-list"
                        @click="store.toList()">
                </Button>
            </div>
        </template>

        <!-- Main Form Section -->
        <div v-if="store.item" class="mt-2">

            <!-- Error Message for Deleted Item -->
            <Message severity="error"
                     class="p-container-message mb-3"
                     :closable="false"
                     icon="pi pi-trash"
                     v-if="store.item.deleted_at">
                <div class="flex align-items-center justify-content-between">
                    <div class="">
                        Deleted {{ store.item.deleted_at }}
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

            <!-- Name Input Field -->
            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="serversv3-name"
                           data-testid="serversv3-name"
                           id="serversv3-name"
                           @update:modelValue="store.watchItem"
                           v-model="store.item.name" required/>
                <label for="serversv3-name">Enter the name</label>
            </FloatLabel>

            <!-- Slug Input Field -->
            <FloatLabel class="my-3" :variant="store.float_label_variants">
                <InputText class="w-full"
                           name="serversv3-slug"
                           data-testid="serversv3-slug"
                           id="serversv3-slug"
                           v-model="store.item.slug" required/>
                <label for="serversv3-slug">Enter the slug</label>
            </FloatLabel>

            <!-- Active Switch Field -->
<!--            <div class="flex items-center gap-2 my-3">-->
<!--                <ToggleSwitch v-bind:false-value="0"-->
<!--                              v-bind:true-value="1"-->
<!--                              size="small"-->
<!--                              name="serversv3-active"-->
<!--                              data-testid="serversv3-active"-->
<!--                              inputId="serversv3-active"-->
<!--                              v-model="store.item.is_active"/>-->
<!--                <label for="serversv3-active">Remember Me</label>-->
<!--            </div>-->


            <div class="p-field p-col-12">
                <label for="hostType">Server Host Type</label>
                <Select id="hostType"
                        v-model="store.item.host_type"
                        :options="store.assets.host_types"
                        optionLabel="name"
                        optionValue="name"
                        placeholder="Select a Host Type" />
            </div>


            <div class="p-field p-col-12">
                <label for="driver">Database Driver</label>
                <Select id="driver"
                        v-model="store.item.driver"
                        :options="store.assets.drivers"
                        optionLabel="name"
                        optionValue="name"
                        placeholder="Select a Driver" />
            </div>

            <!-- Host Input Field -->
            <div class="p-field p-col-12">
                <label for="host">Host</label>
                <InputText id="host"
                           v-model="store.item.host"
                           placeholder="Enter Host"/>
            </div>

            <!-- Port Input Field -->
            <div class="p-field p-col-12">
                <label for="port">Port</label>
                <InputText id="port"
                           v-model="store.item.port"
                           placeholder="Enter Port"/>
            </div>


            <!-- Additional Fields based on Host Type -->
            <div v-if="store.item.host_type === 'mysql' || store.item.host_type === 'digitalocean-mysql'">
                <div class="p-field p-col-12">
                    <label for="username">Username</label>
                    <InputText id="username"
                               v-model="store.item.database_username"
                               placeholder="Enter Username"/>
                </div>
                <div class="p-field p-col-12">
                    <label for="password">Password</label>
                    <InputText id="password"
                               v-model="store.item.database_password"
                               type="password"
                               placeholder="Enter Password"/>
                </div>
            </div>


            <div v-if="store.item.host_type === 'cpanel-mysql'">
                <div class="p-field p-col-12">
                    <label for="cpanelDomain">CPanel Domain</label>
                    <InputText id="cpanelDomain"
                               v-model="store.item.meta.cpanel_domain"
                               placeholder="Enter CPanel Domain"/>
                </div>
                <div class="p-field p-col-12">
                    <label for="cpanelApiToken">CPanel API Token</label>
                    <InputText id="cpanelApiToken"
                               v-model="store.item.meta.cpanel_api_token"
                               placeholder="Enter CPanel API Token"/>
                </div>
            </div>



            <div class="p-field p-col-12">
                <label for="sslMode">Database SSL Mode</label>
                <Select id="sslMode"
                        v-model="store.item.sslmode"
                        :options="store.assets.database_sslmodes"
                        optionLabel="name"
                        optionValue="name"
                        placeholder="Select SSL Mode"/>
            </div>


            <div v-if="store.item.sslmode && store.item.sslmode !== 'disable'">
                <div class="p-field p-col-12">
                    <label for="sslKeyPath">SSL Key Path</label>
                    <InputText id="sslKeyPath"
                               v-model="store.item.meta.ssl_key_path"
                               placeholder="Enter SSL Key Path"/>
                </div>
                <div class="p-field p-col-12">
                    <label for="certPath">CERT Path</label>
                    <InputText id="certPath"
                               v-model="store.item.meta.cert_path"
                               placeholder="Enter CERT Path"/>
                </div>
                <div class="p-field p-col-12">
                    <label for="sslCaPath">SSL CA Path</label>
                    <InputText id="sslCaPath"
                               v-model="store.item.meta.ssl_ca_path"
                               placeholder="Enter SSL CA Path"/>
                </div>
            </div>

            <!-- Test Connection Button -->
            <FloatLabel class="my-3" :variant="store.float_label_variants">
<!--                <Button type="button"-->
<!--                        class="p-button-primary"-->
<!--                        :loading="is_btn_loading_connect"-->
<!--                        @click="store.connect()"-->
<!--                        icon="pi pi-database">-->
<!--                    Test Database Connection-->
<!--                </Button>-->

                <Button label="Test Database Connection"
                        @click="store.connect()"
                        class="p-button-sm"
                        icon="pi pi-database"/>
            </FloatLabel>

        </div>
    </Panel>
</template>
