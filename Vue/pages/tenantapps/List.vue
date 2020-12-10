<script src="./ListJs.js"></script>
<template>
    <div class="form-page-v1-layout">

        <div class="container" >

            <div class="columns">

                <!--left-->
                <div class="column" :class="{'is-6': !page.list_view}">

                    <div class="block" v-if="is_content_loading">
                        <Loader/>
                    </div>

                    <!--card-->
                    <div class="card" v-else-if="page.assets">

                        <!--header-->
                        <header class="card-header">

                            <div class="card-header-title">
                                Tenant's App
                            </div>

                            <div class="card-header-buttons">
                                <div class="field has-addons is-pulled-right">
                                    <p   class="control">
                                        <b-button type="is-light"
                                                  v-on:click="syncTenantApps()"
                                                  :loading="is_btn_loading_sync"
                                                  icon-left="sync">
                                            Sync
                                        </b-button>
                                    </p>

                                    <p class="control">
                                        <b-button type="is-light"
                                                  @click="sync()"
                                                  :loading="is_btn_loading"
                                                  icon-left="redo-alt">
                                        </b-button>
                                    </p>
                                </div>
                            </div>

                        </header>
                        <!--/header-->

                        <!--content-->
                        <div class="card-content">



                            <div class="block" v-if="page.list">


                                <!--actions-->
                                <div class="level">

                                    <!--left-->
                                    <div class="level-left" >
                                        <div  class="level-item">
                                            <b-field >

                                                <b-select placeholder="- Bulk Actions -"
                                                          v-model="page.bulk_action.action">
                                                    <option value="">
                                                        - Bulk Actions -
                                                    </option>
                                                    <option
                                                        v-for="option in page.assets.bulk_actions"
                                                        :value="option.slug"
                                                        :key="option.slug">
                                                        {{ option.name }}
                                                    </option>
                                                </b-select>

                                                <b-select placeholder="- Select Status -"
                                                          v-if="page.bulk_action.action == 'bulk-change-status'"
                                                          v-model="page.bulk_action.data.status">
                                                    <option value="">
                                                        - Select Status -
                                                    </option>
                                                    <option value=1>
                                                        Active
                                                    </option>
                                                    <option value=0>
                                                        Inactive
                                                    </option>
                                                </b-select>


                                                <p class="control">
                                                    <button class="button is-primary"
                                                            @click="actions">
                                                        Apply
                                                    </button>
                                                </p>

                                            </b-field>
                                        </div>
                                    </div>
                                    <!--/left-->


                                    <!--right-->
                                    <div class="level-right">

                                        <div class="level-item">

                                            <b-field>
                                                <p class="control">
                                                    <b-dropdown v-model="query_string.search_by" @input="getList">
                                                        <button slot-scope="{ active }" class="button" slot="trigger">
                                                            <span v-if="query_string.search_by">{{query_string.search_by.charAt(0).toUpperCase() + query_string.search_by.slice(1)}}</span>
                                                            <span v-else > All</span>
                                                            <b-icon :icon="active? 'chevron-up' : 'chevron-down'"></b-icon>
                                                        </button>

                                                        <b-dropdown-item value=''>All</b-dropdown-item>
                                                        <b-dropdown-item value="tenent">Tenent</b-dropdown-item>
                                                        <b-dropdown-item value="app">App</b-dropdown-item>

                                                    </b-dropdown>
                                                </p>

                                                <b-input placeholder="tenant:example or app:example"
                                                         type="text"
                                                         icon="search"
                                                         @input="delayedSearch"
                                                         @keyup.enter.prevent="delayedSearch"
                                                         v-model="query_string.q">
                                                </b-input>

                                                <p class="control">
                                                    <button class="button is-primary"
                                                    @click="getList">
                                                        Filter
                                                    </button>
                                                </p>
                                                <p class="control">
                                                    <button class="button is-primary"
                                                            @click="resetPage">
                                                        Reset
                                                    </button>
                                                </p>
                                                <p class="control">
                                                    <button class="button is-primary"
                                                            @click="toggleFilters()"
                                                            slot="trigger">
                                                        <b-icon icon="ellipsis-v"></b-icon>
                                                    </button>
                                                </p>
                                            </b-field>

                                        </div>

                                    </div>
                                    <!--/right-->

                                </div>
                                <!--/actions-->

                                <!--filters-->
                                <div class="level" v-if="page.show_filters && page.list_view == 'large'" >

                                    <div class="level-left">



                                        <div class="level-item">

                                            <b-field label="">
                                                <b-select placeholder="- Select a status -"
                                                          v-model="query_string.filter"
                                                          @input="setFilter()">
                                                    <option value="">
                                                        - Select a status -
                                                    </option>
                                                    <option value=01>
                                                        Active
                                                    </option>
                                                    <option value=10>
                                                        Not Active
                                                    </option>

                                                </b-select>
                                            </b-field>


                                        </div>
                                        <div class="level-item">

                                            <b-field label="">
                                                <b-select placeholder="- Sort by -"
                                                          v-model="query_string.sort_by"
                                                          @input="setFilter()">
                                                    <option value="">
                                                        -  Sort by -
                                                    </option>
                                                    <option value=id>
                                                        Id
                                                    </option>
                                                    <option value=name>
                                                        Name
                                                    </option>
                                                    <option value=slug>
                                                        Slug
                                                    </option>
                                                    <option value=is_active>
                                                        Is active
                                                    </option>
                                                    <option value=updated_at>
                                                        Updated at
                                                    </option>

                                                </b-select>
                                            </b-field>


                                        </div>
                                        <div class="level-item">

                                            <b-field label="">
                                                <b-dropdown aria-role="list" @input="setFilter()" v-model="query_string.sort_order">
                                                    <button class="button is-primary" type="button" slot="trigger">
                                                        <span v-if="query_string.sort_order === 'desc'">Descending</span>
                                                        <span v-else>Ascending</span>
                                                    </button>

                                                    <b-dropdown-item  value="desc">
                                                        <span>Descending</span>
                                                    </b-dropdown-item>
                                                    <b-dropdown-item  value="asc">
                                                        <span>Ascending</span>
                                                    </b-dropdown-item>
                                                </b-dropdown>
                                            </b-field>


                                        </div>

                                        <div class="level-item">
                                            <div class="field">
                                                <b-checkbox v-model="query_string.trashed"
                                                            @input="getList"
                                                >
                                                    Include Trashed
                                                </b-checkbox>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="level-right">

                                        <div class="level-item">

                                            <b-field>
                                                <b-datepicker style="width: 200px"
                                                              position="is-bottom-left"
                                                              placeholder="- Select a dates -"
                                                              v-model="selected_date"
                                                              @input="setDateRange"
                                                              range>
                                                </b-datepicker>
                                            </b-field>


                                        </div>

                                    </div>


                                </div>
                                <!--/filters-->


                                <!--list-->
                                <div class="block ">

                                    <div class="block" style="margin-bottom: 0px;" >

                                        <div v-if="page.list_view=='medium'">
                                            <ListSmallView/>
                                        </div>

                                        <div v-else>
                                            <ListLargeView/>
                                        </div>



                                    </div>

                                    <hr style="margin-top: 0;"/>

                                    <div class="block" v-if="page.list">
                                        <vh-pagination  :limit="1" :data="page.list"
                                                        @onPageChange="paginate">
                                        </vh-pagination>
                                    </div>

                                </div>
                                <!--/list-->


                            </div>
                        </div>
                        <!--/content-->

                    </div>
                    <!--/card-->


                </div>
                <!--/left-->

                <router-view @eReloadList="getList"></router-view>

            </div>


        </div>

    </div>
</template>


