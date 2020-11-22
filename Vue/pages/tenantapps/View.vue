<script src="./ViewJs.js"></script>
<template>
    <div class="column" v-if="page.assets">

        <div class="card" v-if="item">

            <!--header-->
            <header class="card-header">

                <div class="card-header-title">
                    <span>{{$vaah.limitString(item.name, 15)}}</span>
                </div>

                <div class="card-header-buttons">

                    <div class="field has-addons is-pulled-right">
                        <p class="control">
                            <b-button @click="$vaah.copy(item.id)"  type="is-light">
                                <small><b>#{{item.id}}</b></small>
                            </b-button>
                        </p>
                        <p class="control">
                            <b-button icon-left="edit"
                                      type="is-light"
                                      tag="router-link"
                                      :to="{name:'tenantapps.edit', params:{id: item.id}}">
                                Edit
                            </b-button>
                        </p>

                        <p class="control">


                            <b-dropdown aria-role="list" position="is-bottom-left">
                                <button class="button is-light" slot="trigger">
                                    <b-icon icon="caret-down"></b-icon>
                                </button>

                                <b-dropdown-item aria-role="listitem"
                                                 v-if="!item.tenant.is_database_created_at"
                                                 @click="confirmDatabaseCreate()">
                                    <b-icon icon="database"></b-icon>
                                    Create Database
                                </b-dropdown-item>


                                <b-dropdown-item aria-role="listitem"
                                                 v-if="!item.tenant.is_database_user_created_at"
                                                 @click="confirmDatabaseUserCreate()">
                                    <b-icon icon="user"></b-icon>
                                    Create Database User
                                </b-dropdown-item>

                                <b-dropdown-item aria-role="listitem"
                                                 v-if="item.tenant.is_database_created_at
                                                 && !item.tenant.is_database_user_assigned_at"
                                                 @click="confirmAssignDatabaseUser()">
                                    <b-icon icon="key"></b-icon>
                                    Assign User To Database
                                </b-dropdown-item>

                                <b-dropdown-item aria-role="listitem"
                                                 v-if="item.tenant.is_database_created_at"
                                                 @click="confirmUpdate()">
                                    <b-icon icon="download"></b-icon>
                                    Update
                                </b-dropdown-item>

                                <b-dropdown-item aria-role="listitem"
                                                 v-if="item.tenant.is_database_created_at
                                                 && item.tenant.is_database_user_assigned_at
                                                 && item.tenant.is_database_created_at"
                                                 @click="confirmMigration()">
                                    <b-icon icon="server"></b-icon>
                                    Run Migration
                                </b-dropdown-item>



                                <b-dropdown-item aria-role="listitem"
                                                 v-if="item.tenant.is_database_created_at
                                                 && item.tenant.is_database_user_assigned_at
                                                 && item.last_migrated_at"
                                                 @click="confirmSeed()">
                                    <b-icon icon="bars"></b-icon>
                                    Run Seed
                                </b-dropdown-item>

                                <b-dropdown-item aria-role="listitem"
                                                 v-if="item.tenant.is_database_created_at
                                                 && item.tenant.is_database_user_assigned_at
                                                 && item.last_migrated_at"
                                                 @click="confirmInsertSampleData()">
                                    <b-icon icon="file-download"></b-icon>
                                    Insert Sample Data
                                </b-dropdown-item>

                                <b-dropdown-item aria-role="listitem"
                                                 v-if="!item.deleted_at"
                                                 @click="actions('bulk-trash')"
                                >
                                    <b-icon icon="trash"></b-icon>
                                    Trash
                                </b-dropdown-item>


                                <b-dropdown-item aria-role="listitem"
                                                 v-if="item.deleted_at"
                                                 @click="actions('bulk-restore')"
                                >
                                    <b-icon icon="trash-restore"></b-icon>
                                    Restore
                                </b-dropdown-item>
                                <b-dropdown-item aria-role="listitem"
                                                 @click="confirmDelete()"
                                >
                                    <b-icon icon="eraser"></b-icon>
                                    Delete
                                </b-dropdown-item>

                                <b-dropdown-item aria-role="listitem"
                                                 class="has-text-danger"
                                                 v-if="item.tenant.is_database_created_at
                                                 && item.tenant.is_database_user_assigned_at
                                                 && item.last_migrated_at"
                                                 @click="confirmRollback()">
                                    <b-icon icon="undo"></b-icon>
                                    Migration Rollback
                                </b-dropdown-item>

                                <b-dropdown-item aria-role="listitem"
                                                 class="has-text-danger"
                                                 v-if="item.tenant.is_database_created_at
                                                 && item.tenant.is_database_user_assigned_at
                                                 && item.tenant.is_database_created_at"
                                                 @click="confirmWipeData()">
                                    <b-icon icon="eraser"></b-icon>
                                    Wipe Data
                                </b-dropdown-item>

                                <b-dropdown-item aria-role="listitem"
                                                 v-if="item.tenant.is_database_user_created_at"
                                                 class="has-text-danger"
                                                 @click="confirmDatabaseUserDelete()">
                                    <b-icon icon="user"></b-icon>
                                    Delete Database User
                                </b-dropdown-item>

                                <b-dropdown-item aria-role="listitem"
                                                 v-if="item.tenant.is_database_created_at"
                                                 class="has-text-danger"
                                                 @click="confirmDatabaseDelete()">
                                    <b-icon icon="database"></b-icon>
                                    Delete Database
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
                            v-if="item.deleted_at"
            >
                Deleted {{$vaah.fromNow(item.deleted_at)}}
            </b-notification>

            <!--content-->
            <div class="card-content is-paddingless " v-if="item">



                <div class="block" >


                    <div class="b-table">


                        <div class="table-wrapper">
                            <table class="table is-hoverable">

                                <tbody>


                                <template>
                                    <TableTrView label="Tenant Name"
                                                 :value="item.tenant.name">
                                    </TableTrView>
                                </template>

                                <template v-if="item.tenant">
                                    <tr>
                                        <th align="right">Is Tenant Active</th>
                                        <td colspan="2">
                                            <b-tag v-if="item.tenant.is_active"
                                                      rounded size="is-small"
                                                      type="is-success" >
                                                Yes
                                            </b-tag>
                                            <b-tag v-else rounded size="is-small"
                                                      type="is-danger">
                                                No
                                            </b-tag>

                                        </td>
                                    </tr>
                                </template>

                                <template>
                                    <TableTrTag label="Server"
                                                 is_copiable="true"
                                                 :value="item.tenant.server.name">
                                    </TableTrTag>
                                </template>

                                <template >
                                    <tr>
                                        <th align="right">Is Server Active</th>
                                        <td colspan="2">
                                            <b-tag v-if="item.tenant.server.is_active"
                                                      rounded size="is-small"
                                                      type="is-success" >
                                                Yes
                                            </b-tag>
                                            <b-tag v-else rounded size="is-small"
                                                      type="is-danger">
                                                No
                                            </b-tag>

                                        </td>
                                    </tr>
                                </template>


                                <template>
                                    <TableTrTag label="Database"
                                                 is_copiable="true"
                                                 :value="item.tenant.database_name">
                                    </TableTrTag>
                                </template>

                                <template>
                                    <TableTrTag label="Database Username"
                                                is_copiable="true"
                                                :value="item.tenant.database_username">
                                    </TableTrTag>
                                </template>


                                <template>
                                    <tr>
                                        <th align="right">Database Created</th>
                                        <td colspan="2">
                                            <b-tag v-if="item.tenant.is_database_created_at"
                                                      rounded size="is-small"
                                                      type="is-success" >
                                                Yes
                                            </b-tag>
                                            <b-tag v-else rounded size="is-small" type="is-danger"
                                                      @click="changeStatus(item.id)">
                                                No
                                            </b-tag>

                                        </td>
                                    </tr>
                                </template>

                                <template>
                                    <tr>
                                        <th align="right">Database User Created</th>
                                        <td colspan="2">
                                            <b-tag v-if="item.tenant.is_database_user_created_at"
                                                      rounded size="is-small"
                                                      type="is-success" >
                                                Yes
                                            </b-tag>
                                            <b-tag v-else rounded size="is-small"
                                                      type="is-danger">
                                                No
                                            </b-tag>

                                        </td>
                                    </tr>
                                </template>

                                <template>
                                    <tr>
                                        <th align="right">User Assigned To Database</th>
                                        <td colspan="2">
                                            <b-tag v-if="item.tenant.is_database_user_assigned_at"
                                                      rounded size="is-small"
                                                      type="is-success" >
                                                Yes
                                            </b-tag>
                                            <b-tag v-else rounded size="is-small"
                                                      type="is-danger">
                                                No
                                            </b-tag>

                                        </td>
                                    </tr>
                                </template>


                                <template>
                                    <TableTrActedBy :value="item.tenant.created_by_user"
                                                    label="Created By">
                                    </TableTrActedBy>
                                </template>

                                <template >

                                    <TableTrView label="created_at"
                                                 :value="item.tenant.created_at">
                                    </TableTrView>
                                </template>

                                <template>
                                    <TableTrView label="App Name"
                                                 :value="item.app.name">
                                    </TableTrView>
                                </template>

                                <template>
                                    <TableTrView label="App Slug"
                                                 is_copiable="true"
                                                 :value="item.app.slug">
                                    </TableTrView>
                                </template>

                                <tr>
                                    <th align="right">Installed Vs Current</th>
                                    <td colspan="2">
                                        <b-tag v-if="item.app.version_number>item.version_number"
                                               type='is-danger'>
                                            {{ item.version }}/{{ item.app.version }}
                                        </b-tag>
                                        <b-tag v-else>
                                            {{ item.version }}/{{ item.app.version }}
                                        </b-tag>
                                    </td>
                                </tr>


                                <tr>
                                    <th align="right">Is Active</th>
                                    <td colspan="2">
                                        <b-tooltip label="Change Status" type="is-dark">
                                            <b-button v-if="item.is_active === 1" rounded size="is-small"
                                                      type="is-success" @click="changeStatus(item.id)">
                                                Yes
                                            </b-button>
                                            <b-button v-else rounded size="is-small" type="is-danger"
                                                      @click="changeStatus(item.id)">
                                                No
                                            </b-button>
                                        </b-tooltip>
                                    </td>
                                </tr>


                                <template>
                                    <TableTrDateTime label="Last Migrated"
                                                 :value="item.last_migrated_at">
                                    </TableTrDateTime>
                                </template>

                                <template>
                                    <TableTrDateTime label="Last Seeded"
                                                     :value="item.last_seeded_at">
                                    </TableTrDateTime>
                                </template>

                                <tr>
                                    <th align="right">Notes</th>
                                    <td colspan="2">
                                        {{item.notes}}
                                    </td>
                                </tr>



                                </tbody>



                            </table>
                        </div>

                    </div>


                </div>
            </div>
            <!--/content-->


        </div>




    </div>
</template>


