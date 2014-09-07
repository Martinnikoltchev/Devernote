<?php

    // Include our configuration settings
    require_once 'config.php';

    // Include our OAuth functions
    require_once 'functions.php';
    die("Im at the first death");
    $client = new Evernote\Client(array('token' => $token));
    $userStore = $client->getUserStore();
    $userStore->getUser();
    die('Im at the second death');
    $noteStore = $client->getNoteStore();

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    	$action = $_POST['action'];
    	// If a save is necessary
    	if ($action == 'save'){
	    	$noteContent = $_POST['noteContent'];
	    	$noteTitle = $_POST['noteTitle'];
	    	$noteGuid = $_POST['noteGuid'];
    		try{
    			saveNote($noteStore, $noteTitle, $noteGuid, $noteContent);
    			echo json_encode(array('success'=> True));
    		} catch (EDAMUserException $edue) {
		        // Something was wrong with the note data
		        // See EDAMErrorCode enumeration for error code explanation
		        // http://dev.evernote.com/documentation/reference/Errors.html#Enum_EDAMErrorCode
		        echo json_encode(array('success'=>False, 'error'=>'EDAMUserException'));
		    } catch (EDAMNotFoundException $ednfe) {
		        // Parent Notebook GUID doesn't correspond to an actual notebook
		        echo json_encode(array('success'=>False, 'error'=>'EDAMNotFoundException'));
		    }
		// If need to create notebook
    	} else if ($action == 'create_notebook'){
    		$notebookTitle = $_POST['notebookTitle'];
    		try{
    			$newNotebook = makeNotebook($noteStore, $notebookTitle);
    			$toReturn = array('success'=> True, 'notebook_guid' => $newNoteBook->guid, 'note_name'=>$newNoteBook->name);
    			echo json_encode($toReturn);
    		} catch (EDAMUserException $edue) {
		        // Something was wrong with the note data
		        // See EDAMErrorCode enumeration for error code explanation
		        // http://dev.evernote.com/documentation/reference/Errors.html#Enum_EDAMErrorCode
		        echo json_encode(array('success'=>False, 'error'=>'EDAMUserException'));
		    } catch (EDAMNotFoundException $ednfe) {
		        // Parent Notebook GUID doesn't correspond to an actual notebook
		        echo json_encode(array('success'=>False, 'error'=>'EDAMNotFoundException'));
		    }
		// If need to create note
    	} else if ($action == 'create_note'){
	    	$noteContent = $_POST['noteContent'];
	    	$noteTitle = $_POST['noteTitle'];
	    	if(isset($_POST['notebook_guid'])){
	    		$parentNotebook = $noteStore->getNotebook($_POST['notebook_guid']);
	    	} else {
	    		$parentNotebook = null;
	    	}
	    	try{
	    		$newNote = makeNote($noteStore, $noteTitle, $noteContent, $parentNotebook);
	    		$toReturn = array('success'=>True, 'note_guid'=>$newNote->guid, 'note_name'->$newNote->name);
	    		echo json_encode($toReturn);
	    	} catch (EDAMUserException $edue) {
		        // Something was wrong with the note data
		        // See EDAMErrorCode enumeration for error code explanation
		        // http://dev.evernote.com/documentation/reference/Errors.html#Enum_EDAMErrorCode
		        echo json_encode(array('success'=>False, 'error'=>'EDAMUserException'));
		    } catch (EDAMNotFoundException $ednfe) {
		        // Parent Notebook GUID doesn't correspond to an actual notebook
		        echo json_encode(array('success'=>False, 'error'=>'EDAMNotFoundException'));
		    }
    	}
    }

    die("hi Im here");      
    function makeNotebook($noteStore, $notebookTitle){
        $toMake = new Notebook();
        $toMake->name = $(notebookTitle);

        $newNotebook = $noteStore->createNotebook($toMake);

        return $newNotebook;
    }

    function makeNote($noteStore, $noteTitle, $noteBody, $parentNotebook = null){
        $nBody = '<?xml version="1.0" encoding="UTF-8"?>';
        $nBody .= '<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">';
        $nBody .= '<en-note>' . $noteBody . '</en-note>';
     
        // Create note object
        $ourNote = new Note();
        $ourNote->title = $noteTitle;
        $ourNote->content = $nBody;
     
        // parentNotebook is optional; if omitted, default notebook is used
        if (isset($parentNotebook) && isset($parentNotebook->guid)) {
            $ourNote->notebookGuid = $parentNotebook->guid;
        }
     
        // Attempt to create note in Evernote account
        try {
            $note = $noteStore->createNote($ourNote);
        } catch (EDAMUserException $edue) {
            // Something was wrong with the note data
            // See EDAMErrorCode enumeration for error code explanation
            // http://dev.evernote.com/documentation/reference/Errors.html#Enum_EDAMErrorCode
            print "EDAMUserException: " . $edue;
        } catch (EDAMNotFoundException $ednfe) {
            // Parent Notebook GUID doesn't correspond to an actual notebook
            print "EDAMNotFoundException: Invalid parent notebook GUID";
        }
    // Return created note object
    return $note;
    }

    function saveNote($noteStore, $noteTitle, $noteGuid, $noteBody){
        $nBody = '<?xml version="1.0" encoding="UTF-8"?>';
        $nBody .= '<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">';
        $nBody .= '<en-note>' . $noteBody . '</en-note>';
     
        $ourNote = new Note();
        $ourNote->title = $noteTitle;
        $ourNote->content = $nBody;
        $ourNote->guid = $noteGuid;

        $noteStore->updateNote($ourNote);

        return $ournote;
    }
?>
