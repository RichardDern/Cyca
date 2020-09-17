/**
 * Folders getters
 */
export default {
    /**
     * Return folders tree
     */
    folders: state => {
        return state.folders;
    },
    /**
     * Return currently selected folder
     */
    selectedFolder: state => {
        var folders = state.folders ? state.folders : [];

        return folders.find(folder => folder.is_selected);
    },
    /**
     * Return folder being dragged
     */
    draggedFolder: state => {
        return state.draggedFolder;
    }
}
