/**
 * Actions on folders
 */
export default {
    /*
     * -------------------------------------------------------------------------
     * ----| CRUD |-------------------------------------------------------------
     * -------------------------------------------------------------------------
     */

    /**
     * Display a listing of the resource.
     */
    async index({ getters, commit, dispatch }, { folders, show }) {
        commit("setFolders", folders);

        if (show) {
            if (show === "unread_items") {
                show = getters.getUnreadItemsFolder;
            }

            dispatch("show", { folder: show });
        } else {
            dispatch("show", {});
        }
    },

    /**
     * Display the specified resource.
     */
    show(
        { commit, getters, dispatch },
        { folder, deselectDocuments = true, updateFeedItems = true }
    ) {
        const currentSelectedFolder = getters.selectedFolder;

        if (!folder) {
            folder = currentSelectedFolder;
        } else if (Number.isInteger(folder)) {
            folder = getters.folders.find((f) => f.id === folder);
        }

        commit("setSelectedFolder", folder);

        if (deselectDocuments) {
            dispatch("documents/selectDocuments", [], { root: true });
        }

        api.get(route("folder.show", folder)).then(function (response) {
            dispatch(
                "documents/index",
                { documents: response, updateFeedItems: updateFeedItems },
                { root: true }
            );
        });
    },

    /**
     * Load folder's details
     */
    async loadDetails({ dispatch }, folder) {
        if (!folder.details_loaded && !folder.details_loading) {
            dispatch("updateProperties", {
                folderId: folder.id,
                newProperties: {
                    details_loading: true,
                },
            });

            let response = await api.get(route("folder.details", folder));

            response.details_loaded = true;
            response.details_loading = false;

            dispatch("updateProperties", {
                folderId: folder.id,
                newProperties: response,
            });
        }
    },

    /**
     * Store a newly created resource in storage.
     */
    store({ dispatch }, { title, parent_id, group_id }) {
        return api
            .post(route("folder.store"), {
                title,
                parent_id,
                group_id,
            })
            .then((data) => {
                dispatch("index", { folders: data });
            })
            .catch((error) => console.error(error));
    },

    /**
     * Update the specified resource in storage.
     */
    async update({ dispatch }, { folder, newProperties }) {
        const data = await api.put(
            route("folder.update", folder),
            newProperties
        );

        dispatch("updateProperties", {
            folderId: folder.id,
            newProperties: data,
        });
    },

    /**
     * Update the specified resource in storage.
     */
    updateProperties({ commit, getters }, { folderId, newProperties }) {
        const folder = getters.folders.find((f) => f.id == folderId);

        if (!folder) {
            return;
        }

        commit("update", { folder: folder, newProperties: newProperties });
    },

    updatePermission({ dispatch }, { folder, user, ability, granted }) {
        api.post(route("folder.set_permission", folder), {
            ability: ability,
            granted: granted,
            user_id: user,
        }).then(function (response) {
            dispatch("updateProperties", {
                folderId: folder.id,
                newProperties: response,
            });
        });
    },

    /**
     * Remove the specified resource from storage.
     */
    async destroy({ dispatch }, folder) {
        const data = await api.delete(route("folder.destroy", folder));

        dispatch("index", { folders: data });
    },

    /**
     * Toggle specified folder expanded/collapsed
     */
    toggleExpanded({ dispatch }, folder) {
        dispatch("update", {
            folder: folder,
            newProperties: {
                ...folder,
                ...{
                    is_expanded: !folder.is_expanded,
                },
            },
        });
    },

    /**
     * Toggle specified folder's branch expanded/collapsed
     */
    async toggleBranch({ commit }, folder) {
        const response = await api.post(route("folder.toggle_branch", folder));

        commit("setFolders", response);
    },

    /*
     * -------------------------------------------------------------------------
     * ----| Drag'n'drop |------------------------------------------------------
     * -------------------------------------------------------------------------
     */

    /**
     * Remind folder being dragged
     */
    startDraggingFolder({ commit }, folder) {
        commit("setDraggedFolder", folder);
    },

    /**
     * Forget folder being dragged
     */
    stopDraggingFolder({ commit }) {
        commit("setDraggedFolder", null);
    },

    /**
     * Drop something into specified folder
     */
    async dropIntoFolder({ getters, dispatch }, folder) {
        var sourceFolder = getters.draggedFolder;

        if (!sourceFolder) {
            sourceFolder = getters.selectedFolder;

            await dispatch(
                "documents/dropIntoFolder",
                { sourceFolder: sourceFolder, targetFolder: folder },
                { root: true }
            );

            return;
        }

        if (
            sourceFolder.parent_id === folder.id ||
            sourceFolder.id === folder.id
        ) {
            return;
        }

        const newProperties = {
            parent_id: folder.id,
        };

        await dispatch("update", {
            folder: sourceFolder,
            newProperties: {
                ...sourceFolder,
                ...newProperties,
            },
        }).then(function () {
            dispatch("groups/show", {}, { root: true });
        });
    },
};
