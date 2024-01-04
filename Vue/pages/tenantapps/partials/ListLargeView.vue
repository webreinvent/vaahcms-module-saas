<script src="./ListLargeViewJs.js"></script>
<template>
    <div>
        <b-table :data="page.list_is_empty ? [] : page.list.data"
                 :checkable="true"
                 :checked-rows.sync="page.bulk_action.selected_items"
                 checkbox-position="left"
                 :hoverable="true"
                 :row-class="setRowClass"
        >

            <template>
                <b-table-column v-slot="props" field="id" label="ID" width="40" numeric>
                    {{ props.row.id }}
                </b-table-column>

                <b-table-column v-slot="props" field="tenant_name" label="Tenant">
                    {{ props.row.tenant.name }}
                </b-table-column>

                <b-table-column v-slot="props" field="app_name" label="App">
                    {{ props.row.app.name }}
                </b-table-column>

                <b-table-column v-slot="props" width="20%" field="version" label="Install/Current Version"
                                >
                    <b-tag v-if="props.row.app.version_number>props.row.version_number"
                           type='is-danger'>
                        {{ props.row.version }}/{{ props.row.app.version }}
                    </b-tag>
                    <b-tag v-else>
                        {{ props.row.version }}/{{ props.row.app.version }}
                    </b-tag>

                </b-table-column>

                <b-table-column v-slot="props" width="10%" field="is_active" label="Is Active">
                    <b-tooltip label="Change Status" type="is-dark">
                        <b-button v-if="props.row.is_active === 1" rounded size="is-small"
                                  type="is-success" @click="changeStatus(props.row.id)">
                            Yes
                        </b-button>
                        <b-button v-else rounded size="is-small" type="is-danger"
                                  @click="changeStatus(props.row.id)">
                            No
                        </b-button>
                    </b-tooltip>
                </b-table-column>
                <b-table-column v-slot="props" width="15%" field="updated_at" label="Updated At">

                    {{ localTimeShortFormat(props.row.updated_at) }}
                </b-table-column>


                <b-table-column v-slot="props" field="actions" label="Actions"
                                width="40">

                    <b-tooltip label="View" type="is-dark">
                        <b-button size="is-small"
                                  @click="setActiveItem(props.row)"
                                  icon-left="chevron-right">
                        </b-button>
                    </b-tooltip>


                </b-table-column>


            </template>

            <template slot="empty">
                <section class="section">
                    <div class="content has-text-grey has-text-centered">
                        <p>Nothing here.</p>
                    </div>
                </section>
            </template>

        </b-table>
    </div>
</template>

