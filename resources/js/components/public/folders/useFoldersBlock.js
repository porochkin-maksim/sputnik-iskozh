import { useFoldersNavigation } from './useFoldersNavigation.js';
import { useFoldersFiles }      from './useFoldersFiles.js';

export function useFoldersBlock (props) {
    const navigation = useFoldersNavigation(props);
    const files      = useFoldersFiles(navigation);

    return {
        ...navigation,
        ...files,
    };
}
