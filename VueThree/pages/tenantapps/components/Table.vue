<script setup>
import { vaah } from '@/vaahvue/pinia/vaah'
import { useTenantAppStore } from '../../../stores/store-tenantapps'

const store = useTenantAppStore();
const useVaah = vaah();

</script>

<template>

    <div v-if="store?.list">
        <!--table-->
        <DataTable :value="store.list.data"
                   dataKey="id"
                   :rowClass="store.setRowClass"
                   class="p-datatable-sm p-datatable-hoverable-rows"
                   :nullSortOrder="-1"
                   v-model:selection="store.action.items"
                   stripedRows
                   responsiveLayout="scroll">

            <Column selectionMode="multiple"
                    v-if="store.isListView()"
                    headerStyle="width: 3em">
            </Column>

            <Column field="id" header="ID" :style="{width: '80px'}" :sortable="true">
            </Column>

            <Column field="tenant" header="Tenant"
                    class="overflow-wrap-anywhere"
                    :sortable="true">

                <template #body="prop">
                    <span v-if="prop.data.saas_tenant">
                        {{prop.data.saas_tenant.name}}
                    </span>
                    <span v-else>
                        -
                    </span>
                </template>

            </Column>

            <Column field="app" header="App"
                    class="overflow-wrap-anywhere"
                    :sortable="true">

                <template #body="prop">
                    <span v-if="prop.data.saas_app">
                        {{prop.data.saas_app.name}}
                    </span>
                    <span v-else>
                        -
                    </span>
                </template>

            </Column>

            <Column field="install/current_vision" header="Install/Current Version"
                    class="overflow-wrap-anywhere"
                    :sortable="true">

                <template #body="prop">
                    <Badge style="background-color: lightgrey; color: black;">
                        {{prop.data.version}} / {{prop.data.version}}
                    </Badge>

                </template>


            </Column>


            <Column field="updated_at" header="Updated"
                    v-if="store.isListView() && !store.isMobile"
                    style="width:150px;"
                    :sortable="true">

                <template #body="prop">
                    {{useVaah.toLocalTimeShortFormat(prop.data.updated_at)}}
                </template>

            </Column>

            <Column field="is_active" v-if="store.isListView() && !store.isMobile"
                    :sortable="true"
                    style="width:100px;"
                    header="Is Active">

                <template #body="prop">
                    <ToggleSwitch v-model.bool="prop.data.is_active"
                                  data-testid="tenantsapps-table-is-active"
                                  v-bind:false-value="0"  v-bind:true-value="1"
                                  size="small"
                                  variant="success"
                                  @input="store.toggleIsActive(prop.data)">
                    </ToggleSwitch>
                </template>

            </Column>

            <Column field="actions" style="width:150px;"
                    :style="{width: store.getActionWidth() }"
                    :header="store.getActionLabel()">

                <template #body="prop">
                    <div class="p-inputgroup ">

                        <Button class="p-button-tiny p-button-text"
                                data-testid="tenantsapps-table-to-view"
                                v-tooltip.top="'View'"
                                @click="store.toView(prop.data)"
                                icon="pi pi-eye" />

<!--                        <Button class="p-button-tiny p-button-text"-->
<!--                                data-testid="tenantapps-table-to-edit"-->
<!--                                v-tooltip.top="'Update'"-->
<!--                                @click="store.toEdit(prop.data)"-->
<!--                                icon="pi pi-pencil" />-->

                        <Button class="p-button-tiny p-button-danger p-button-text"
                                data-testid="tenantsapps-table-action-trash"
                                v-if="store.isListView() && !prop.data.deleted_at && !store.isMobile"
                                @click="store.itemAction('trash', prop.data)"
                                v-tooltip.top="'Trash'"
                                icon="pi pi-trash" />


                        <Button class="p-button-tiny p-button-success p-button-text"
                                data-testid="tenantsapps-table-action-restore"
                                v-if="store.isListView() && prop.data.deleted_at && !store.isMobile"
                                @click="store.itemAction('restore', prop.data)"
                                v-tooltip.top="'Restore'"
                                icon="pi pi-replay" />


                    </div>

                </template>


            </Column>

            <template #empty>
                <div class="text-center py-3">
                    No records found.
                </div>
            </template>

        </DataTable>
        <!--/table-->

        <!--paginator-->
        <Paginator v-if="store.query.rows"
                   v-model:rows="store.query.rows"
                   :totalRecords="store.list.total"
                   :first="((store.query.page??1)-1)*store.query.rows"
                   @page="store.paginate($event)"
                   :rowsPerPageOptions="store.rows_per_page"
                   class="bg-white-alpha-0 pt-2">
        </Paginator>
        <!--/paginator-->


    </div>

</template>
