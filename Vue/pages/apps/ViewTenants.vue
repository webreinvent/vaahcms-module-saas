<script src="./ViewTenantsJs.js"></script>
<template>
    <div class="column" v-if="page.assets">

        <div class="block" v-if="is_content_loading">
            <Loader/>
        </div>

        <div class="card" v-else>
            <!--header-->
            <header v-if="item" class="card-header">
                <div  class="card-header-title">
                    <span>{{$vaah.limitString(item.name, 30)}} > Tenants</span>
                </div>

                <div class="card-header-buttons">

                    <div class="field has-addons is-pulled-right">
                        <p class="control">
                            <b-button @click="$vaah.copy(item.id)"  type="is-light">
                                <small><b>#{{item.id}}</b></small>
                            </b-button>

                        </p>

                        <p v-if="hasPermission('oho-can-update-roles') || hasPermission('oho-can-manage-roles')" class="control">


                            <b-dropdown aria-role="list" position="is-bottom-left">
                                <button class="button is-light" slot="trigger">
                                    <b-icon icon="caret-down"></b-icon>
                                </button>

                                <b-dropdown-item aria-role="listitem"
                                                 @click="bulkActions('1')"
                                >
                                    Active All Permission
                                </b-dropdown-item>

                                <b-dropdown-item aria-role="listitem"
                                                 @click="bulkActions('0')"
                                >
                                    Inactive All Permission
                                </b-dropdown-item>

                            </b-dropdown>


                        </p>


                        <p class="control">
                            <b-button type="is-light"
                                    @click="resetActiveItem()"
                                    icon-left="times">
                            </b-button>
                        </p>




                    </div>


                </div>

            </header>
            <!--/header-->

            <b-notification type="is-danger"
                            :closable="false"
                            class="is-light is-small"
                            v-if="item && item.deleted_at"
            >
                Deleted {{$vaah.fromNow(item.deleted_at)}}
            </b-notification>

            <!--content-->
            <div class="card-content">

                <div class="block is-flex flex-column"  v-if="items && items.list">

                    <div class="level">
                        <div class="level-left">
                            <div class="level-item">




                            </div>
                        </div>
                        <div class="level-right">
                            <b-field>
                                <b-input style="width: 100%" placeholder="Search Tenants"
                                         type="search"
                                         icon="search"
                                         @input="delayedSearch"
                                         @keyup.enter.prevent="delayedSearch"
                                         v-model="search_item">>

                                </b-input>

                                <p class="control">
                                    <button class="button is-primary"
                                            @click="resetPage">
                                        Reset
                                    </button>
                                </p>
                            </b-field>
                        </div>
                    </div>



                    <!--filters-->

                    <!--/filters-->

                </div>

                <div class="block"  v-if="items && items.list">

                    <!--filters-->

                    <!--/filters-->



                    <b-table :data="items.list.data"
                             :hoverable="true"
                    >

                        <template slot-scope="props">

                            <b-table-column field="id" label="Tenant Name" >
                                <b-tooltip label="Copy Slug" type="is-dark">
                                    <b-button size="is-small"
                                              class="is-light"
                                              @click="$vaah.copy(props.row.slug)">
                                        {{ props.row.name }}
                                    </b-button>
                                </b-tooltip>
                            </b-table-column>


                            <b-table-column field="id" label="App Version" >
                                <b-button size="is-small"
                                          :type="{'is-danger': props.row.pivot.version_number < item.version_number}"
                                          class="is-light">
                                    v{{ props.row.pivot.version }}/v{{item.version}}
                                </b-button>
                            </b-table-column>


                            <b-table-column field="has_role" class="has-text-centered" label="Has App">
                                <b-button v-if="props.row.pivot.is_active === 1" rounded size="is-small"
                                          type="is-success" @click="changePermission(props.row)">
                                    Yes
                                </b-button>
                                <b-button v-else rounded size="is-small" type="is-danger"
                                          @click="changePermission(props.row)">
                                    No
                                </b-button>
                            </b-table-column>



                            <b-table-column  field="status" label="Tenant Status" numeric>
                                <b-button v-if="props.row.is_active == 1" rounded
                                          class="is-success"
                                          type="is-small" @click="changeItemStatus(props.row.id)">
                                    Active
                                </b-button>

                                <b-button v-else rounded class="is-danger"
                                          type="is-small"
                                          @click="changeItemStatus(props.row.id)">
                                    Inactive
                                </b-button>
                            </b-table-column>




                        </template>

                    </b-table>

                    <hr style="margin-top: 0;"/>

                </div>

                <div class="block" v-if="items">
                    <vh-pagination  :limit="1" :data="items.list"
                                    @onPageChange="getItemPermissions">
                    </vh-pagination>
                </div>
            </div>
            <!--/content-->





        </div>




    </div>



</template>


