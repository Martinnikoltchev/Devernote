<?php

    // Include our configuration settings
    require_once 'config.php';

    // Include our OAuth functions
    require_once 'functions.php';

    $client = new Evernote\Client(array('token' => $token));
    $userStore = $client->getUserStore();
    $userStore->getUser();

    $noteStore = $client->getNoteStore();

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    	$noteContent = $_POST['noteContent'];
    	$noteTitle = $_POST['noteTitle'];
    	$noteGuid = $_POST['noteGuid'];
    	$action = $_POST['action'];
    	if ($action == 'save'){
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
    	}
    }

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