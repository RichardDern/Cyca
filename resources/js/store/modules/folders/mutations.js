/**
 * Folders mutations
 */
export default {
    /**
     * Store folders list
     * @param {*} state
     * @param {*} folders
     */
    setFolders(state, folders) {
        state.folders = folders;
    },

    /**
     * Unselect all folders, and mark specified folder as selected
     * @param {*} state
     * @param {*} folder
     */
    setSelectedFolder(state, folder) {
        state.folders.find(f => f.is_selected = false);

        folder.is_selected = true;
    },

    /**
     * Update folder's properties
     * @param {*} state
     * @param {*} param1
     */
    update(state, {folder, newProperties}) {
        for(var property in newProperties) {
            folder[property] = newProperties[property];
        }
    },

    /**
     * Store folder being dragged during a drag'n'drop process
     * @param {*} state
     * @param {*} folder
     */
    setDraggedFolder(state, folder) {
        state.draggedFolder = folder;
    },
}
