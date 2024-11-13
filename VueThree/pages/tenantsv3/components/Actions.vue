<script  setup>
import {ref, reactive, watch, onMounted} from 'vue';
import {useRoute} from 'vue-router';

import { useTenantV3Store } from '@/stores/store-tenantsv3'

const store = useTenantV3Store();
const route = useRoute();

onMounted(async () => {
    store.getListSelectedMenu();
    store.getListBulkMenu();
});

//--------selected_menu_state
const selected_menu_state = ref();
const toggleSelectedMenuState = (event) => {
    selected_menu_state.value.toggle(event);
};
//--------/selected_menu_state

//--------bulk_menu_state
const bulk_menu_state = ref();
const toggleBulkMenuState = (event) => {
    bulk_menu_state.value.toggle(event);
};
//--------/bulk_menu_state
</script>

<template>
    <div>


        <!--actions-->
        <div :class="{'flex justify-between items-start': store.isListView()}" class="mt-2 mb-2">

            <!--left-->
            <div v-if="store.view === 'list'">

                <!--selected_menu-->
                <Button size="small"
                        type="button"
                        @click="toggleSelectedMenuState"
                        data-testid="tenantsv3-actions-menu"
                        aria-haspopup="true"
                        aria-controls="overlay_menu">
                    <i class="pi pi-angle-down"></i>
                    <Badge v-if="store.action.items.length > 0"
                           :value="store.action.items.length" />
                </Button>
                <Menu ref="selected_menu_state"
                      :model="store.list_selected_menu"
                      :popup="true" />
                <!--/selected_menu-->

            </div>
            <!--/left-->

            <!--right-->
            <div >

                <InputGroup>
                    <InputText v-model="store.query.filter.q"
                               @keyup.enter="store.delayedSearch()"
                               size="small"
                               @keyup.enter.native="store.delayedSearch()"
                               @keyup.13="store.delayedSearch()"
                               data-testid="tenantsv3-actions-search"
                               placeholder="Search"/>
                    <Button @click="store.delayedSearch()"
                            size="small"
                            data-testid="tenantsv3-actions-search-button"
                            icon="pi pi-search"/>
                    <Button v-if="!store.isMobile"
                            as="router-link"
                            :to="`/tenantsv3/filters`"
                            type="button"
                            size="small"
                            :disabled="Object.keys(route.params).length"
                            data-testid="tenantsv3-actions-show-filters"
                    >
                        <span style="font-weight: var(--p-button-label-font-weight);" >Filters</span>
                        <Badge v-if="store.count_filters > 0" :value="store.count_filters"></Badge>
                    </Button>

                    <Button
                            type="button"
                            icon="pi pi-filter-slash"
                            data-testid="tenantsv3-actions-reset-filters"
                            size="small"
                            :label="store?.isMobile ? '' : 'Reset'"
                            @click="store.resetQuery()" />

                    <!--bulk_menu-->
                    <Button
                            type="button"
                            size="small"
                            @click="toggleBulkMenuState"
                            severity="danger" outlined
                            data-testid="tenantsv3-actions-bulk-menu"
                            aria-haspopup="true"
                            aria-controls="bulk_menu_state"
                    >
                        <i class="pi pi-ellipsis-v"></i>
                    </Button>
                    <Menu ref="bulk_menu_state"
                          :model="store.list_bulk_menu"
                          :popup="true" />
                    <!--/bulk_menu-->
                </InputGroup>

            </div>
            <!--/right-->

        </div>
        <!--/actions-->

    </div>
</template>
