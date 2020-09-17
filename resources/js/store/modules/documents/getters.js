/**
 * Documents getters
 */
export default {
    /**
     * Return documents list
     */
    documents: state => {
        return collect(state.documents).sortBy('title').all();
    },
    /**
     * Return currently selected documents
     */
    selectedDocuments: state => {
        return state.selectedDocuments;
    },
    /**
     * Return the first selected document
     */
    selectedDocument: state => {
        return collect(state.selectedDocuments).first();
    },
    /**
     * Return documents being dragged
     */
    draggedDocuments: state => {
        return state.draggedDocuments;
    }
}
