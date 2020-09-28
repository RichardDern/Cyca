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
    async index({ commit }) {
        const data = await api.get(route("folder.index"));

        commit("setFolders", data);
    },

    /**
     * Display the specified resource.
     */
    async show({ commit, getters, dispatch }, folder) {
        const currentSelectedFolder = getters.selectedFolder;

        if (!folder) {
            folder = currentSelectedFolder;
        }

        if (currentSelectedFolder && currentSelectedFolder.id !== folder.id) {
            commit("setSelectedFolder", folder);
            dispatch("documents/selectDocuments", [], { root: true });
        }

        const documents = await api.get(route("folder.show", folder));

        dispatch("documents/index", documents, { root: true });
    },

    /**
     * Store a newly created resource in storage.
     */
    async store({ getters, commit }, { title, parent_id }) {
        const parentFolder = getters.folders.find(f => f.id === parent_id);
        const data = await api.post(route("folder.store"), {
            title,
            parent_id
        });

        commit("setFolders", data);
    },

    /**
     * Update the specified resource in storage.
     */
    async update({ commit }, { folder, newProperties }) {
        const data = await api.put(
            route("folder.update", folder),
            newProperties
        );

        commit("update", { folder: folder, newProperties: data });
    },

    /**
     * Remove the specified resource from storage.
     */
    async destroy({ commit }, folder) {
        const data = await api.delete(route("folder.destroy", folder));

        commit("setFolders", data);
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
                    is_expanded: !folder.is_expanded
                }
            }
        });
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
            ).then(function() {
                dispatch("index");
                dispatch("show");
            });

            return;
        }

        const newProperties = {
            parent_id: folder.id
        };

        if (
            sourceFolder.parent_id === folder.id ||
            sourceFolder.id === folder.id
        ) {
            return;
        }

        await dispatch("update", {
            folder: sourceFolder,
            newProperties: {
                ...sourceFolder,
                ...newProperties
            }
        }).then(function() {
            dispatch("index");
            dispatch("show");
        });
    }
};
